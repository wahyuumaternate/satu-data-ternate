<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DatasetApprovalController extends Controller
{
    /**
     * Display pending datasets for approval
     */
    // public function index(Request $request)
    // {
    //     $query = Dataset::with(['user', 'approvedBy'])
    //         ->where('approval_status', 'pending')
    //         ->orderBy('created_at', 'desc');

    //     // Filter by topic
    //     if ($request->has('topic') && $request->topic !== '') {
    //         $query->where('topic', $request->topic);
    //     }

    //     // Filter by classification
    //     if ($request->has('classification') && $request->classification !== '') {
    //         $query->where('classification', $request->classification);
    //     }

    //     // Search by title or description
    //     if ($request->has('search') && $request->search !== '') {
    //         $search = $request->search;
    //         $query->where(function($q) use ($search) {
    //             $q->where('title', 'like', "%{$search}%")
    //               ->orWhere('description', 'like', "%{$search}%")
    //               ->orWhere('filename', 'like', "%{$search}%")
    //               ->orWhereHas('user', function($userQuery) use ($search) {
    //                   $userQuery->where('name', 'like', "%{$search}%");
    //               });
    //         });
    //     }

    //     $pendingDatasets = $query->paginate(15)->withQueryString();

    //     // 🆕 UPDATED: Enhanced statistics with revision support
    //     $stats = [
    //         'pending' => Dataset::where('approval_status', 'pending')->count(),
    //         'approved' => Dataset::where('approval_status', 'approved')->count(),
    //         'rejected' => Dataset::where('approval_status', 'rejected')->count(),
    //         'revision' => Dataset::where('approval_status', 'revision')->count(),
    //         'needs_attention' => Dataset::whereIn('approval_status', ['rejected', 'revision'])->count(),
    //         'total' => Dataset::count(),
    //         'today_submissions' => Dataset::whereDate('created_at', today())->count(),
    //         'this_week' => Dataset::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count()
    //     ];

    //     // Get filter options
    //     $filterOptions = [
    //         'topics' => Dataset::distinct()->whereNotNull('topic')->pluck('topic')->sort(),
    //         'classifications' => Dataset::distinct()->whereNotNull('classification')->pluck('classification')->sort(),
    //     ];

    //     return view('admin.dataset-approval.index', compact('pendingDatasets', 'stats', 'filterOptions'));
    // }

    /**
     * Show dataset details for approval
     */
    public function show(Dataset $dataset)
    {
        $dataset->load(['user', 'approvedBy']);
        
        return view('admin.dataset-approval.show', compact('dataset'));
    }

    /**
     * Approve dataset
     */
    public function approve(Request $request, Dataset $dataset)
    {
        $request->validate([
            'approval_notes' => 'nullable|string|max:1000'
        ]);

        try {
            // Check if user has permission
            if (!Auth::user()->hasRole(['admin', 'reviewer'])) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menyetujui dataset.');
            }

            // Use helper method from model
            $dataset->approve(Auth::id(), $request->approval_notes);

            // Log the action
            Log::info('Dataset approved', [
                'dataset_id' => $dataset->id,
                'approved_by' => Auth::user()->name,
                'notes' => $request->approval_notes
            ]);

            return redirect()
                ->route('admin.dataset-approval.index')
                ->with('success', 'Dataset "' . $dataset->title . '" berhasil disetujui dan dipublikasikan.');

        } catch (\Exception $e) {
            Log::error('Dataset approval failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyetujui dataset: ' . $e->getMessage());
        }
    }

    /**
     * 🆕 NEW: Request revision for dataset
     */
    public function requestRevision(Request $request, Dataset $dataset)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
            'approval_notes' => 'nullable|string|max:1000'
        ]);

        try {
            // Check if user has permission
            if (!Auth::user()->hasRole(['admin', 'reviewer'])) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk meminta revisi dataset.');
            }

            // Request revision using helper method from model
            $dataset->requestRevision(Auth::id(), $request->rejection_reason, $request->approval_notes);

            // Log the action
            Log::info('Dataset revision requested', [
                'dataset_id' => $dataset->id,
                'requested_by' => Auth::user()->name,
                'reason' => $request->rejection_reason,
                'notes' => $request->approval_notes
            ]);

            // Send notification (implement as needed)
            $this->notifyDatasetOwner($dataset, 'revision_requested', $request->rejection_reason);

            return redirect()
                ->route('admin.dataset-approval.index')
                ->with('success', 'Permintaan revisi berhasil dikirim ke pemilik dataset "' . $dataset->title . '".');

        } catch (\Exception $e) {
            Log::error('Dataset revision request failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat meminta revisi dataset: ' . $e->getMessage());
        }
    }

    /**
     * Reject dataset
     */
    public function reject(Request $request, Dataset $dataset)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
            'approval_notes' => 'nullable|string|max:1000'
        ]);

        try {
            // Check if user has permission
            if (!Auth::user()->hasRole(['admin', 'reviewer'])) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menolak dataset.');
            }

            // Use helper method from model
            $dataset->reject(Auth::id(), $request->rejection_reason, $request->approval_notes);

            // Log the action
            Log::info('Dataset rejected', [
                'dataset_id' => $dataset->id,
                'rejected_by' => Auth::user()->name,
                'reason' => $request->rejection_reason,
                'notes' => $request->approval_notes
            ]);

            // Send notification (implement as needed)
            $this->notifyDatasetOwner($dataset, 'rejected', $request->rejection_reason);

            return redirect()
                ->route('admin.dataset-approval.index')
                ->with('success', 'Dataset "' . $dataset->title . '" berhasil ditolak.');

        } catch (\Exception $e) {
            Log::error('Dataset rejection failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menolak dataset: ' . $e->getMessage());
        }
    }

    /**
     * Resubmit rejected dataset for approval
     */
    public function resubmit(Dataset $dataset)
    {
        try {
            if (!$dataset->canBeResubmitted()) {
                return redirect()->back()->with('error', 'Dataset ini tidak dapat diajukan ulang.');
            }

            // Use helper method from model
            $dataset->resubmit();

            // Log the action
            Log::info('Dataset resubmitted', [
                'dataset_id' => $dataset->id,
                'resubmitted_by' => Auth::user()->name
            ]);

            return redirect()
                ->route('admin.dataset-approval.index')
                ->with('success', 'Dataset "' . $dataset->title . '" berhasil diajukan ulang untuk persetujuan.');

        } catch (\Exception $e) {
            Log::error('Dataset resubmission failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengajukan ulang dataset: ' . $e->getMessage());
        }
    }

    /**
     * 🆕 NEW: Bulk approval actions
     */
    public function bulkApprovalAction(Request $request)
    {
        $request->validate([
            'dataset_ids' => 'required|array',
            'dataset_ids.*' => 'exists:datasets,id',
            'action' => 'required|in:approve,reject,request_revision',
            'rejection_reason' => 'required_if:action,reject,request_revision|string|max:1000',
            'approval_notes' => 'nullable|string|max:1000'
        ]);

        try {
            // Check permissions
            if (!Auth::user()->hasRole(['admin', 'reviewer'])) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk melakukan aksi ini.');
            }

            $processedCount = 0;
            $action = $request->action;

            foreach ($request->dataset_ids as $datasetId) {
                try {
                    $dataset = Dataset::findOrFail($datasetId);
                    
                    switch ($action) {
                        case 'approve':
                            $dataset->approve(Auth::id(), $request->approval_notes);
                            break;
                        case 'reject':
                            $dataset->reject(Auth::id(), $request->rejection_reason, $request->approval_notes);
                            break;
                        case 'request_revision':
                            $dataset->requestRevision(Auth::id(), $request->rejection_reason, $request->approval_notes);
                            break;
                    }
                    
                    $processedCount++;
                    
                } catch (\Exception $e) {
                    Log::error("Failed to {$action} dataset ID {$datasetId}: " . $e->getMessage());
                }
            }

            $actionText = match($action) {
                'approve' => 'disetujui',
                'reject' => 'ditolak',
                'request_revision' => 'diminta revisi'
            };

            return redirect()->back()->with('success', "Berhasil {$actionText} {$processedCount} dataset.");
            
        } catch (\Exception $e) {
            Log::error('Bulk approval action failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat melakukan aksi bulk approval.');
        }
    }

    /**
     * View approved datasets
     */
    public function approved(Request $request)
    {
        $query = Dataset::with(['user', 'approvedBy'])
            ->where('approval_status', 'approved')
            ->orderBy('approved_at', 'desc');

        // Apply filters
        if ($request->has('topic') && $request->topic !== '') {
            $query->where('topic', $request->topic);
        }

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('approved_date') && $request->approved_date !== '') {
            $query->whereDate('approved_at', $request->approved_date);
        }

        $approvedDatasets = $query->paginate(15)->withQueryString();

        return view('admin.dataset-approval.approved', compact('approvedDatasets'));
    }

    /**
     * View rejected datasets
     */
    public function rejected(Request $request)
    {
        $query = Dataset::with(['user', 'approvedBy'])
            ->where('approval_status', 'rejected')
            ->orderBy('approved_at', 'desc');

        // Apply filters
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('rejected_date') && $request->rejected_date !== '') {
            $query->whereDate('approved_at', $request->rejected_date);
        }

        if ($request->has('rejected_by') && $request->rejected_by !== '') {
            $query->where('approved_by', $request->rejected_by);
        }

        $rejectedDatasets = $query->paginate(15)->withQueryString();

        return view('admin.dataset-approval.rejected', compact('rejectedDatasets'));
    }

    /**
     * 🆕 NEW: View datasets needing revision
     */
    public function revision(Request $request)
    {
        $query = Dataset::with(['user', 'approvedBy'])
            ->where('approval_status', 'revision')
            ->orderBy('approved_at', 'desc');

        // Apply filters
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('revision_date') && $request->revision_date !== '') {
            $query->whereDate('approved_at', $request->revision_date);
        }

        $revisionDatasets = $query->paginate(15)->withQueryString();

        return view('admin.dataset-approval.revision', compact('revisionDatasets'));
    }

    /**
     * 🆕 NEW: Dashboard method for reviewers/admins
     */
    public function dashboard()
    {
        // // Check permissions
        // if (!Auth::user()->hasRole(['admin', 'reviewer'])) {
        //     abort(403);
        // }

        $stats = $this->getApprovalStats();

        // Get recent datasets needing attention
        $recentDatasets = Dataset::whereIn('approval_status', [
                Dataset::APPROVAL_PENDING,
                Dataset::APPROVAL_REVISION
            ])
            ->with(['user'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Get approval activity for the last 7 days
        $approvalActivity = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $approvalActivity[] = [
                'date' => $date->format('M d'),
                'approved' => Dataset::where('approval_status', Dataset::APPROVAL_APPROVED)
                    ->whereDate('approved_at', $date)
                    ->count(),
                'rejected' => Dataset::where('approval_status', Dataset::APPROVAL_REJECTED)
                    ->whereDate('approved_at', $date)
                    ->count(),
                'revision' => Dataset::where('approval_status', Dataset::APPROVAL_REVISION)
                    ->whereDate('approved_at', $date)
                    ->count(),
            ];
        }

        return view('admin.dataset-approval.dashboard', compact('stats', 'recentDatasets', 'approvalActivity'));
    }

    /**
     * 🆕 NEW: Get approval statistics
     */
    public function getApprovalStats()
    {
        $stats = [
            'pending' => Dataset::where('approval_status', Dataset::APPROVAL_PENDING)->count(),
            'approved' => Dataset::where('approval_status', Dataset::APPROVAL_APPROVED)->count(),
            'rejected' => Dataset::where('approval_status', Dataset::APPROVAL_REJECTED)->count(),
            'revision' => Dataset::where('approval_status', Dataset::APPROVAL_REVISION)->count(),
            'needs_attention' => Dataset::whereIn('approval_status', [
                Dataset::APPROVAL_REJECTED, 
                Dataset::APPROVAL_REVISION
            ])->count(),
            'today_submissions' => Dataset::whereDate('created_at', today())->count(),
            'this_week_submissions' => Dataset::whereBetween('created_at', [
                now()->startOfWeek(), 
                now()->endOfWeek()
            ])->count(),
        ];

        // Get trending topics
        $stats['trending_topics'] = Dataset::selectRaw('topic, COUNT(*) as count')
            ->whereNotNull('topic')
            ->whereBetween('created_at', [now()->subDays(30), now()])
            ->groupBy('topic')
            ->orderByDesc('count')
            ->limit(5)
            ->pluck('count', 'topic');

        return $stats;
    }

    /**
     * 🆕 NEW: Quick actions for approval (AJAX)
     */
    public function quickAction(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,request_revision',
            'reason' => 'required_if:action,reject,request_revision|string|max:500',
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            $dataset = Dataset::findOrFail($id);
            
            // Check permissions
            if (!Auth::user()->hasRole(['admin', 'reviewer'])) {
                return response()->json(['success' => false, 'message' => 'Unauthorized']);
            }

            switch ($request->action) {
                case 'approve':
                    $dataset->approve(Auth::id(), $request->notes);
                    $message = 'Dataset berhasil disetujui';
                    break;
                case 'reject':
                    $dataset->reject(Auth::id(), $request->reason, $request->notes);
                    $message = 'Dataset berhasil ditolak';
                    break;
                case 'request_revision':
                    $dataset->requestRevision(Auth::id(), $request->reason, $request->notes);
                    $message = 'Revisi berhasil diminta';
                    break;
            }

            // Send notification (implement as needed)
            $this->notifyDatasetOwner($dataset, $request->action, $request->reason ?? $request->notes);

            return response()->json([
                'success' => true,
                'message' => $message,
                'new_status' => $dataset->approval_status
            ]);

        } catch (\Exception $e) {
            Log::error('Quick action failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses permintaan'
            ]);
        }
    }

    /**
     * 🆕 NEW: Send notification to dataset owner
     */
    private function notifyDatasetOwner($dataset, $action, $message = null)
    {
        try {
            // You can implement email notifications here
            // For now, we'll just log the notification
            Log::info("Dataset {$action} notification", [
                'dataset_id' => $dataset->id,
                'dataset_title' => $dataset->title,
                'owner_id' => $dataset->user_id,
                'owner_email' => $dataset->user->email ?? 'unknown',
                'action' => $action,
                'message' => $message,
                'reviewer' => Auth::user()->name
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send notification: ' . $e->getMessage());
        }
    }

    /**
     * Display pending datasets for approval (original method updated)
     */
    public function index(Request $request)
    {
        // Get status from query parameter, default to 'pending'
        $status = $request->get('status', 'pending');
        $validStatuses = ['pending', 'approved', 'rejected', 'revision'];
        
        if (!in_array($status, $validStatuses)) {
            return redirect()->route('admin.dataset-approval.index', ['status' => 'pending']);
        }

        $query = Dataset::with(['user', 'approvedBy'])
            ->where('approval_status', $status);

        // Apply filters
        if ($request->has('topic') && $request->topic !== '') {
            $query->where('topic', $request->topic);
        }

        if ($request->has('classification') && $request->classification !== '') {
            $query->where('classification', $request->classification);
        }

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('filename', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('date_filter') && $request->date_filter !== '') {
            if ($status === 'pending') {
                $query->whereDate('created_at', $request->date_filter);
            } else {
                $query->whereDate('approved_at', $request->date_filter);
            }
        }

        if ($request->has('reviewer') && $request->reviewer !== '' && $status !== 'pending') {
            $query->where('approved_by', $request->reviewer);
        }

        // Order by appropriate date field
        if ($status === 'pending') {
            $query->orderBy('created_at', 'desc');
        } else {
            $query->orderBy('approved_at', 'desc');
        }

        $datasets = $query->paginate(15)->withQueryString();

        // Get enhanced statistics with revision support
        $stats = [
            'pending' => Dataset::where('approval_status', 'pending')->count(),
            'approved' => Dataset::where('approval_status', 'approved')->count(),
            'rejected' => Dataset::where('approval_status', 'rejected')->count(),
            'revision' => Dataset::where('approval_status', 'revision')->count(),
            'needs_attention' => Dataset::whereIn('approval_status', ['rejected', 'revision'])->count(),
            'total' => Dataset::count(),
            'today_submissions' => Dataset::whereDate('created_at', today())->count(),
            'this_week' => Dataset::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count()
        ];

        // Get filter options
        $filterOptions = [
            'topics' => Dataset::distinct()->whereNotNull('topic')->pluck('topic')->sort(),
            'classifications' => Dataset::distinct()->whereNotNull('classification')->pluck('classification')->sort(),
        ];

        // Use the unified status view
        return view('admin.dataset-approval.status', compact('datasets', 'stats', 'filterOptions'));
    }
}