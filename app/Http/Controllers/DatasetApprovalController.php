<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DatasetApprovalController extends Controller
{
    /**
     * Display pending datasets for approval
     */
    public function index(Request $request)
    {
        $query = Dataset::with(['user', 'approvedBy'])
            ->where('approval_status', 'pending')
            ->orderBy('created_at', 'desc');

        // Filter by topic
        if ($request->has('topic') && $request->topic !== '') {
            $query->where('topic', $request->topic);
        }

        // Filter by classification
        if ($request->has('classification') && $request->classification !== '') {
            $query->where('classification', $request->classification);
        }

        // Search by title or description
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('filename', 'like', "%{$search}%");
            });
        }

        $pendingDatasets = $query->paginate(15);

        // Get statistics
        $stats = [
            'pending' => Dataset::where('approval_status', 'pending')->count(),
            'approved' => Dataset::where('approval_status', 'approved')->count(),
            'rejected' => Dataset::where('approval_status', 'rejected')->count(),
            'total' => Dataset::count()
        ];

        return view('admin.dataset-approval.index', compact('pendingDatasets', 'stats'));
    }

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
   /**
     * Approve dataset
     */
    public function approve(Request $request, Dataset $dataset)
    {
        $request->validate([
            'approval_notes' => 'nullable|string|max:1000'
        ]);

        // 🎯 MENGGUNAKAN HELPER METHOD
        $dataset->approve(Auth::id(), $request->approval_notes);

        return redirect()
            ->route('admin.dataset-approval.index')
            ->with('success', 'Dataset has been approved and published successfully.');
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

        // 🎯 MENGGUNAKAN HELPER METHOD
        $dataset->reject(Auth::id(), $request->rejection_reason, $request->approval_notes);

        return redirect()
            ->route('admin.dataset-approval.index')
            ->with('success', 'Dataset has been rejected.');
    }

    /**
     * Resubmit rejected dataset for approval
     */
    public function resubmit(Dataset $dataset)
    {
        if (!$dataset->isRejected()) {
            return redirect()->back()->with('error', 'Only rejected datasets can be resubmitted.');
        }

        // 🎯 MENGGUNAKAN HELPER METHOD
        $dataset->resubmit();

        return redirect()
            ->route('admin.dataset-approval.index')
            ->with('success', 'Dataset has been resubmitted for approval.');
    }
    /**
     * Bulk approve datasets
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'dataset_ids' => 'required|array',
            'dataset_ids.*' => 'exists:datasets,id',
            'approval_notes' => 'nullable|string|max:1000'
        ]);

        // Parse JSON if needed
        $datasetIds = $request->dataset_ids;
        if (is_string($datasetIds)) {
            $datasetIds = json_decode($datasetIds, true);
        }

        $count = Dataset::whereIn('id', $datasetIds)
            ->where('approval_status', 'pending')
            ->update([
                'approval_status' => 'approved',
                'publish_status' => 'published',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'approval_notes' => $request->approval_notes,
                'published_at' => now()
            ]);

        return redirect()
            ->route('admin.dataset-approval.index')
            ->with('success', "{$count} datasets have been approved and published.");
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

        // Filter by approved date
        if ($request->has('approved_date') && $request->approved_date !== '') {
            $query->whereDate('approved_at', $request->approved_date);
        }

        $approvedDatasets = $query->paginate(15);

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

        // Filter by rejected date
        if ($request->has('rejected_date') && $request->rejected_date !== '') {
            $query->whereDate('approved_at', $request->rejected_date);
        }

        $rejectedDatasets = $query->paginate(15);

        return view('admin.dataset-approval.rejected', compact('rejectedDatasets'));
    }

    /**
     * Resubmit rejected dataset for approval
     */
    // public function resubmit(Dataset $dataset)
    // {
    //     if ($dataset->approval_status !== 'rejected') {
    //         return redirect()->back()->with('error', 'Only rejected datasets can be resubmitted.');
    //     }

    //     $dataset->update([
    //         'approval_status' => 'pending',
    //         'rejection_reason' => null,
    //         'approval_notes' => null,
    //         'approved_by' => null,
    //         'approved_at' => null,
    //         'publish_status' => 'draft' // Reset ke draft
    //     ]);

    //     return redirect()
    //         ->route('admin.dataset-approval.index')
    //         ->with('success', 'Dataset has been resubmitted for approval.');
    // }

    /**
     * Dashboard statistics
     */
    public function dashboard()
    {
        $stats = [
            'pending' => Dataset::where('approval_status', 'pending')->count(),
            'approved_today' => Dataset::where('approval_status', 'approved')
                ->whereDate('approved_at', today())->count(),
            'rejected_today' => Dataset::where('approval_status', 'rejected')
                ->whereDate('approved_at', today())->count(),
            'total_datasets' => Dataset::count(),
        ];

        // Recent activities
        $recentApprovals = Dataset::with(['user', 'approvedBy'])
            ->where('approval_status', 'approved')
            ->orderBy('approved_at', 'desc')
            ->limit(5)
            ->get();

        $recentSubmissions = Dataset::with('user')
            ->where('approval_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'stats' => $stats,
            'recent_approvals' => $recentApprovals,
            'recent_submissions' => $recentSubmissions
        ]);
    }
}