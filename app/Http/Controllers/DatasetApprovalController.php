<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DatasetApprovalController extends Controller
{
    public function __construct()
    {
        $user = Auth::user();

        if (!$user->hasRole(['super-admin', 'reviewer'])) {
            abort(403, 'Hanya Super Admin dan Reviewer yang memiliki akses ke approval management.');
        }
    }

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
            $query->where(function ($q) use ($search) {
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
            'total' => Dataset::count(),
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
    public function approve(Request $request, Dataset $dataset)
    {
        $request->validate([
            'approval_notes' => 'nullable|string|max:1000',
        ]);

        try {
            // 🎯 MENGGUNAKAN HELPER METHOD
            $dataset->approve(Auth::id(), $request->approval_notes);

            return redirect()->route('admin.dataset-approval.index')->with('success', 'Dataset has been approved and published successfully.');
        } catch (\Exception $e) {
            Log::error('Dataset approval error', [
                'dataset_id' => $dataset->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Failed to approve dataset. Please try again.');
        }
    }

    /**
     * Reject dataset
     */
    public function reject(Request $request, Dataset $dataset)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
            'approval_notes' => 'nullable|string|max:1000',
        ]);

        try {
            // 🎯 MENGGUNAKAN HELPER METHOD
            $dataset->reject(Auth::id(), $request->rejection_reason, $request->approval_notes);

            return redirect()->route('admin.dataset-approval.index')->with('success', 'Dataset has been rejected.');
        } catch (\Exception $e) {
            Log::error('Dataset rejection error', [
                'dataset_id' => $dataset->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Failed to reject dataset. Please try again.');
        }
    }

    /**
     * Resubmit rejected dataset for approval
     */
    public function resubmit(Dataset $dataset)
    {
        if (!$dataset->isRejected()) {
            return redirect()->back()->with('error', 'Only rejected datasets can be resubmitted.');
        }

        try {
            // 🎯 MENGGUNAKAN HELPER METHOD
            $dataset->resubmit();

            return redirect()->route('admin.dataset-approval.index')->with('success', 'Dataset has been resubmitted for approval.');
        } catch (\Exception $e) {
            Log::error('Dataset resubmit error', [
                'dataset_id' => $dataset->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Failed to resubmit dataset. Please try again.');
        }
    }

    /**
     * Bulk approve datasets - FIXED VERSION
     */
    public function bulkApprove(Request $request)
    {
        // Validasi input dengan lebih baik
        $validator = Validator::make($request->all(), [
            'dataset_ids' => 'required|string',
            'approval_notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            Log::warning('Bulk approve validation failed', [
                'errors' => $validator->errors()->toArray(),
                'request' => $request->all(),
            ]);

            return redirect()->back()->withErrors($validator)->with('error', 'Invalid request data. Please select at least one dataset.');
        }

        try {
            // Parse dataset_ids dari JSON string
            $datasetIdsJson = $request->dataset_ids;

            Log::info('Bulk approve request received', [
                'raw_dataset_ids' => $datasetIdsJson,
                'type' => gettype($datasetIdsJson),
            ]);

            // Decode JSON
            $datasetIds = json_decode($datasetIdsJson, true);

            // Validasi hasil decode
            if (!is_array($datasetIds) || empty($datasetIds)) {
                Log::error('Failed to decode dataset IDs', [
                    'raw' => $datasetIdsJson,
                    'decoded' => $datasetIds,
                ]);

                return redirect()->back()->with('error', 'Invalid dataset selection. Please try again.');
            }

            // Validasi bahwa semua IDs adalah integer
            $datasetIds = array_filter($datasetIds, function ($id) {
                return is_numeric($id) && $id > 0;
            });

            if (empty($datasetIds)) {
                return redirect()->back()->with('error', 'No valid datasets selected.');
            }

            Log::info('Processing bulk approve', [
                'dataset_ids' => $datasetIds,
                'count' => count($datasetIds),
                'user_id' => Auth::id(),
            ]);

            // Ambil datasets yang pending
            $datasets = Dataset::whereIn('id', $datasetIds)->where('approval_status', 'pending')->get();

            if ($datasets->isEmpty()) {
                Log::warning('No pending datasets found for bulk approve', [
                    'requested_ids' => $datasetIds,
                ]);

                return redirect()->back()->with('error', 'No pending datasets found to approve.');
            }

            // Approve setiap dataset
            $successCount = 0;
            $failedCount = 0;
            $errors = [];

            foreach ($datasets as $dataset) {
                try {
                    $dataset->update([
                        'approval_status' => 'approved',
                        'publish_status' => 'published',
                        'approved_by' => Auth::id(),
                        'approved_at' => now(),
                        'approval_notes' => $request->approval_notes,
                        'published_at' => now(),
                    ]);

                    $successCount++;

                    Log::info('Dataset approved in bulk', [
                        'dataset_id' => $dataset->id,
                        'title' => $dataset->title,
                    ]);
                } catch (\Exception $e) {
                    $failedCount++;
                    $errors[] = "Failed to approve dataset ID {$dataset->id}: " . $e->getMessage();

                    Log::error('Failed to approve dataset in bulk', [
                        'dataset_id' => $dataset->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Return response based on results
            if ($successCount > 0 && $failedCount === 0) {
                return redirect()
                    ->route('admin.dataset-approval.index')
                    ->with('success', "{$successCount} datasets have been approved and published successfully.");
            } elseif ($successCount > 0 && $failedCount > 0) {
                return redirect()
                    ->route('admin.dataset-approval.index')
                    ->with('warning', "{$successCount} datasets approved, but {$failedCount} failed. Check logs for details.");
            } else {
                return redirect()->back()->with('error', 'Failed to approve datasets. Please try again or contact administrator.');
            }
        } catch (\Exception $e) {
            Log::error('Bulk approve error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return redirect()->back()->with('error', 'An error occurred during bulk approval. Please try again.');
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
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%");
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
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%");
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
     * Dashboard statistics
     */
    public function dashboard()
    {
        $stats = [
            'pending' => Dataset::where('approval_status', 'pending')->count(),
            'approved_today' => Dataset::where('approval_status', 'approved')->whereDate('approved_at', today())->count(),
            'rejected_today' => Dataset::where('approval_status', 'rejected')->whereDate('approved_at', today())->count(),
            'total_datasets' => Dataset::count(),
        ];

        // Recent activities
        $recentApprovals = Dataset::with(['user', 'approvedBy'])
            ->where('approval_status', 'approved')
            ->orderBy('approved_at', 'desc')
            ->limit(5)
            ->get();

        $recentSubmissions = Dataset::with('user')->where('approval_status', 'pending')->orderBy('created_at', 'desc')->limit(5)->get();

        return response()->json([
            'stats' => $stats,
            'recent_approvals' => $recentApprovals,
            'recent_submissions' => $recentSubmissions,
        ]);
    }
}
