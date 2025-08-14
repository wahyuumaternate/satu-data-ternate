<?php

namespace App\Http\Controllers;

use App\Models\Dataset;
use App\Imports\DynamicImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DatasetController extends Controller
{
    public function index(Request $request)
    {
        
        $query = Dataset::with(['user', 'approvedBy'])
        ->where('approval_status', 'approved'); // hanya tampilkan yang approved

        // Base filter - only show user's own datasets (optional)
        // $query->where('user_id', Auth::id());

        // Search functionality - pencarian di multiple kolom
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('organization', 'like', "%{$search}%")
                  ->orWhere('filename', 'like', "%{$search}%")
                  ->orWhere('original_filename', 'like', "%{$search}%")
                  ->orWhere('sector', 'like', "%{$search}%")
                  ->orWhere('data_source', 'like', "%{$search}%")
                  // Search dalam tags JSON
                  ->orWhereJsonContains('tags', $search)
                  ->orWhere('tags', 'like', "%{$search}%");
            });
        }

        // Filter by topic
        if ($request->has('topic') && $request->topic) {
            $query->where('topic', $request->topic);
        }

        // Filter by classification
        if ($request->has('classification') && $request->classification) {
            $query->where('classification', $request->classification);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by approval status
        if ($request->has('approval_status') && $request->approval_status) {
            $query->where('approval_status', $request->approval_status);
            
            // Jika filter approved atau rejected, urutkan berdasarkan approved_at
            if (in_array($request->approval_status, ['approved', 'rejected'])) {
                $query->orderBy('approved_at', 'desc');
            }
        }

        // Filter by organization
        if ($request->has('organization') && $request->organization) {
            $query->where('organization', 'like', "%{$request->organization}%");
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by file type
        if ($request->has('file_type') && $request->file_type) {
            $query->where('file_type', $request->file_type);
        }

        // Filter by data size
        if ($request->has('data_size') && $request->data_size) {
            switch ($request->data_size) {
                case 'small':
                    $query->where('total_rows', '<', 1000);
                    break;
                case 'medium':
                    $query->whereBetween('total_rows', [1000, 10000]);
                    break;
                case 'large':
                    $query->where('total_rows', '>', 10000);
                    break;
            }
        }

        // Filter untuk rejected datasets - search berdasarkan rejection reason
        if ($request->has('rejection_reason') && $request->rejection_reason) {
            $query->where('rejection_reason', 'like', "%{$request->rejection_reason}%");
        }

        // Filter by approver (untuk yang sudah approved/rejected)
        if ($request->has('approver') && $request->approver) {
            $query->whereHas('approvedBy', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->approver}%");
            });
        }

        // Filter by publish status
        if ($request->has('publish_status') && $request->publish_status) {
            $query->where('publish_status', $request->publish_status);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        // Validasi sort columns untuk security
        $allowedSortColumns = [
            'created_at', 'title', 'view_count', 'download_count', 
            'total_rows', 'total_columns', 'file_size', 'updated_at'
        ];

        if (in_array($sortBy, $allowedSortColumns)) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Default ordering jika tidak ada approval_status filter
        if (!$request->has('approval_status')) {
            $query->orderBy('created_at', 'desc');
        }

        // Paginate results
        $datasets = $query->paginate(12)->withQueryString();

        // Process tags untuk setiap dataset
        $datasets->getCollection()->transform(function ($dataset) {
            // Decode JSON tags jika masih dalam format JSON
            if (is_string($dataset->tags)) {
                $dataset->tags = json_decode($dataset->tags, true) ?? [];
            }
            
            // Pastikan tags adalah array
            if (!is_array($dataset->tags)) {
                $dataset->tags = [];
            }

            return $dataset;
        });

        // Get statistics untuk dashboard/info cards
        $stats = [
            'total_datasets' => Dataset::count(),
            'pending_approval' => Dataset::where('approval_status', 'pending')->count(),
            'approved_datasets' => Dataset::where('approval_status', 'approved')->count(),
            'rejected_datasets' => Dataset::where('approval_status', 'rejected')->count(),
            'published_datasets' => Dataset::where('publish_status', 'published')->count(),
            'draft_datasets' => Dataset::where('publish_status', 'draft')->count(),
            'total_views' => Dataset::sum('view_count'),
            'total_downloads' => Dataset::sum('download_count'),
        ];

        // Get filter options untuk dropdown (dengan data yang ada di database)
        $filterOptions = [
            'topics' => Dataset::distinct()->pluck('topic')->filter()->sort()->values(),
            'classifications' => Dataset::distinct()->pluck('classification')->filter()->sort()->values(),
            'organizations' => Dataset::distinct()->pluck('organization')->filter()->sort()->values(),
            'file_types' => Dataset::distinct()->pluck('file_type')->filter()->sort()->values(),
            'sectors' => Dataset::distinct()->pluck('sector')->filter()->sort()->values(),
            'approval_statuses' => [
                'pending' => 'Pending Review',
                'approved' => 'Approved',
                'rejected' => 'Rejected'
            ],
            'publish_statuses' => [
                'draft' => 'Draft',
                'published' => 'Published',
                'archived' => 'Archived'
            ]
        ];

        // Jika ada filter approval_status, tambahkan info spesifik
        $currentFilter = $request->approval_status;
        $pageTitle = 'All Datasets';
        
        switch ($currentFilter) {
            case 'pending':
                $pageTitle = 'Pending Datasets';
                break;
            case 'approved':
                $pageTitle = 'Approved Datasets';
                break;
            case 'rejected':
                $pageTitle = 'Rejected Datasets';
                // Get common rejection reasons untuk filter
                $filterOptions['rejection_reasons'] = Dataset::where('approval_status', 'rejected')
                                                            ->whereNotNull('rejection_reason')
                                                            ->distinct()
                                                            ->pluck('rejection_reason')
                                                            ->filter()
                                                            ->map(function ($reason) {
                                                                // Potong text untuk dropdown
                                                                return strlen($reason) > 50 ? substr($reason, 0, 50) . '...' : $reason;
                                                            })
                                                            ->unique()
                                                            ->sort()
                                                            ->values();
                break;
        }

        // Add breadcrumb info based on filters
        $breadcrumbs = ['Home', 'Dataset'];
        if ($currentFilter) {
            $breadcrumbs[] = ucfirst($currentFilter);
        }

        return view('dataset.index', compact(
            'datasets', 
            'stats', 
            'filterOptions', 
            'pageTitle', 
            'currentFilter',
            'breadcrumbs'
        ));
    }


    public function create()
    {
        return view('dataset.create');
    }

    // Method store untuk form lengkap yang baru
   // Method store untuk form lengkap yang baru
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // 10MB max
            'title' => 'required|string|max:255|unique:datasets,title',
            'description' => 'required|string',
            'tags' => 'required|string',
            'topic' => 'required|string',
            'classification' => 'required|in:publik,internal,terbatas,rahasia',
            'status' => 'required|in:sementara,tetap',
            'license' => 'nullable|string',
            'sector' => 'nullable|string',
            'responsible_person' => 'nullable|string',
            'contact' => 'nullable|string',
            'data_source' => 'nullable|string',
            'data_period' => 'nullable|string',
            'update_frequency' => 'nullable|string',
            'geographic_coverage' => 'nullable|string',
        ], [
             'title.unique' => 'Judul dataset sudah digunakan, silakan pilih judul lain.',
            ]);

        try {
            DB::beginTransaction();
            
            $file = $request->file('file');
            $originalFilename = $file->getClientOriginalName();
            $filename = time() . '_' . $originalFilename;
            $fileSize = $file->getSize();
            $fileType = $file->getClientOriginalExtension();
            
            // Store file
            $filePath = $file->storeAs('datasets', $filename, 'public');
            
            // Determine publish status
            $publishStatus = 'published';
            if ($request->has('action') && $request->action === 'draft') {
                $publishStatus = 'draft';
            }
            // Create dataset record first with all required fields
            $dataset = Dataset::create([
                'title' => $request->title,
                'description' => $request->description,
                'tags' => $this->processTags($request->tags),
                'filename' => $filename,
                'original_filename' => $originalFilename,
                'file_path' => $filePath,
                'file_size' => $fileSize,
                'file_type' => $fileType,
                'topic' => $request->topic,
                'classification' => $request->classification,
                'status' => $request->status,
                'license' => $request->license ?? '',
                'sector' => $request->sector ?? '',
                'responsible_person' => $request->responsible_person ?? '',
                'contact' => $request->contact ?? '',
                'data_source' => $request->data_source ?? '',
                'data_period' => $request->data_period ?? '',
                'update_frequency' => $request->update_frequency ?? '',
                'geographic_coverage' => $request->geographic_coverage ?? '',
                'user_id' => Auth::user()->id,
                'organization' => Auth::user()->organization ?? '',
                'publish_status' => $publishStatus,
                'headers' => [], // Will be filled by import
                'data' => [], // Will be filled by import
                'total_rows' => 0,
                'total_columns' => 0
            ]);

            Log::info('Dataset record created with ID: ' . $dataset->id);
            
            // Import Excel data using updated DynamicImport
            Excel::import(new DynamicImport($dataset->id), $file);
            
            DB::commit();
            
            // Handle different response types
            if ($request->has('action') && $request->action === 'draft') {
                return redirect()->route('dataset.index')
                    ->with('success', 'Dataset berhasil disimpan sebagai draft: ' . $originalFilename);
            }
            $slug = Str::slug($request->title);
            return redirect()->route('dataset.show', $slug)
                ->with('success', 'Dataset berhasil diimport: ' . $originalFilename);
                
        } catch (\Exception $e) {
            DB::rollback();
            
            // Delete uploaded file if exists
            if (isset($filePath) && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            
            Log::error('Dataset store failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    
    // // Method import yang sudah ada (tidak diubah)
    // public function import(Request $request)
    // {
    //     $request->validate([
    //         'file' => 'required|mimes:xlsx,xls,csv|max:10240'
    //     ]);

    //     try {
    //         DB::beginTransaction();
            
    //         $file = $request->file('file');
    //         $filename = time() . '_' . $file->getClientOriginalName();
            
    //         // Log file info
    //         Log::info('Importing file: ' . $filename);
    //         Log::info('File size: ' . $file->getSize() . ' bytes');
    //         Log::info('File extension: ' . $file->getClientOriginalExtension());
            
    //         // Preview data untuk debugging (optional)
    //         if ($request->has('preview')) {
    //             $preview = $this->previewExcelData($file);
    //             return response()->json($preview);
    //         }
            
    //         Excel::import(new DynamicImport($filename), $file);
            
    //         DB::commit();
            
    //         return redirect()->route('dataset.index')
    //             ->with('success', 'Data berhasil diimport dari file: ' . $file->getClientOriginalName());
                
    //     } catch (\Exception $e) {
    //         DB::rollback();
            
    //         Log::error('Import failed: ' . $e->getMessage());
    //         Log::error('Stack trace: ' . $e->getTraceAsString());
            
    //         return redirect()->back()
    //             ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    //     }
    // }

    /**
     * Preview Excel data untuk debugging
     */
    private function previewExcelData($file)
    {
        try {
            $array = Excel::toArray(new DynamicImport('preview'), $file);
            
            return [
                'success' => true,
                'headers' => !empty($array[0]) ? array_keys($array[0][0]) : [],
                'sample_data' => !empty($array[0]) ? array_slice($array[0], 0, 3) : [],
                'total_rows' => !empty($array[0]) ? count($array[0]) : 0
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function show($slug, Request $request)
    {
        $dataset = Dataset::where('slug', $slug)->firstOrFail();
        
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 10);
        
        // Apply filters if any
        $filteredData = $this->applyFilters($dataset->data, $request);
        
        // Get paginated data from filtered results
        $total = count($filteredData);
        $offset = ($page - 1) * $perPage;
        
        $paginatedData = [
            'data' => array_slice($filteredData, $offset, $perPage),
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
        
        // Debug info
        if ($request->has('debug')) {
            return response()->json([
                'headers' => $dataset->headers,
                'sample_data' => array_slice($dataset->data, 0, 2),
                'data_structure' => !empty($dataset->data) ? array_keys($dataset->data[0]) : [],
                'filters_applied' => $request->get('filter', []),
                'search_term' => $request->get('search', ''),
                'filtered_count' => $total,
                'original_count' => count($dataset->data)
            ]);
        }
        
        return view('dataset.show', compact('dataset', 'paginatedData'));
    }

    public function edit($id)
    {
        
       $dataset = Dataset::where('slug', $id)->firstOrFail();

        return view('dataset.edit', compact('dataset'));
    }
public function update(Request $request, $id)
{
    $dataset = Dataset::findOrFail($id);
    
    // // Check if user has permission to edit this dataset
    // if ($dataset->user_id !== Auth::user()->id && !Auth::user()->hasRole('admin')) {
    //     abort(403, 'Unauthorized action.');
    // }

    $request->validate([
        'file' => 'nullable|mimes:xlsx,xls,csv|max:10240', // 10MB max, optional for edit
        'title' => 'required|string|max:255|unique:datasets,title,' . $dataset->id,
        'description' => 'required|string',
        'tags' => 'required|string',
        'topic' => 'required|string',
        'classification' => 'required|in:publik,internal,terbatas,rahasia',
        'status' => 'required|in:sementara,tetap',
        'license' => 'nullable|string',
        'sector' => 'nullable|string',
        'responsible_person' => 'nullable|string',
        'contact' => 'nullable|string',
        'data_source' => 'nullable|string',
        'data_period' => 'nullable|string',
        'update_frequency' => 'nullable|string',
        'geographic_coverage' => 'nullable|string',
    ], [
        'title.unique' => 'Judul dataset sudah digunakan, silakan pilih judul lain.',
    ]);

    try {
        DB::beginTransaction();
        
        $hasNewFile = $request->hasFile('file');
        $shouldReprocessData = $request->has('action') && $request->action === 'update_and_reprocess';
        
        // Handle file replacement if new file is uploaded
        if ($hasNewFile) {
            $file = $request->file('file');
            $originalFilename = $file->getClientOriginalName();
            $filename = time() . '_' . $originalFilename;
            $fileSize = $file->getSize();
            $fileType = $file->getClientOriginalExtension();
            
            // Delete old file
            if ($dataset->file_path && Storage::disk('public')->exists($dataset->file_path)) {
                Storage::disk('public')->delete($dataset->file_path);
            }
            
            // Store new file
            $filePath = $file->storeAs('datasets', $filename, 'public');
            
            // Update file-related fields
            $dataset->filename = $filename;
            $dataset->original_filename = $originalFilename;
            $dataset->file_path = $filePath;
            $dataset->file_size = $fileSize;
            $dataset->file_type = $fileType;
        }
        
        // Update dataset information
        $dataset->update([
            'title' => $request->title,
            'description' => $request->description,
            'tags' => $this->processTags($request->tags),
            'topic' => $request->topic,
            'classification' => $request->classification,
            'status' => $request->status,
            'license' => $request->license ?? '',
            'sector' => $request->sector ?? '',
            'responsible_person' => $request->responsible_person ?? '',
            'contact' => $request->contact ?? '',
            'data_source' => $request->data_source ?? '',
            'data_period' => $request->data_period ?? '',
            'update_frequency' => $request->update_frequency ?? '',
            'geographic_coverage' => $request->geographic_coverage ?? '',
            'updated_at' => now(),
        ]);

        Log::info('Dataset updated with ID: ' . $dataset->id);
        
        // Re-import data if new file uploaded or reprocess requested
        if ($hasNewFile || $shouldReprocessData) {
            // Reset data fields before reimporting
            $dataset->update([
                'headers' => [],
                'data' => [],
                'total_rows' => 0,
                'total_columns' => 0
            ]);
            
            // Import Excel data using updated DynamicImport
            if ($hasNewFile) {
                Excel::import(new DynamicImport($dataset->id), $file);
                $successMessage = 'Dataset dan file berhasil diupdate: ' . $dataset->original_filename;
            } else {
                // Reprocess existing file
                $existingFilePath = storage_path('app/public/' . $dataset->file_path);
                if (file_exists($existingFilePath)) {
                    Excel::import(new DynamicImport($dataset->id), $existingFilePath);
                    $successMessage = 'Dataset berhasil diupdate dan data berhasil diproses ulang.';
                } else {
                    throw new \Exception('File dataset tidak ditemukan untuk diproses ulang.');
                }
            }
        } else {
            $successMessage = 'Informasi dataset berhasil diupdate.';
        }
        
        DB::commit();
        
        // Determine redirect route
        $slug = Str::slug($request->title);
        if ($dataset->slug !== $slug) {
            $dataset->update(['slug' => $slug]);
        }
        
        return redirect()->route('dataset.show', $dataset->slug ?? $dataset->id)
            ->with('success', $successMessage);
            
    } catch (\Exception $e) {
        DB::rollback();
        
        // Delete uploaded file if exists and there was an error
        if (isset($filePath) && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
        
        Log::error('Dataset update failed: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return redirect()->back()
            ->with('error', 'Terjadi kesalahan saat mengupdate dataset: ' . $e->getMessage())
            ->withInput();
    }
}

    /**
     * Apply filters and search to dataset
     */
    private function applyFilters(array $data, Request $request): array
    {
        $filteredData = $data;
        
        // Apply column filters
        $filters = $request->get('filter', []);
        if (!empty($filters)) {
            foreach ($filters as $column => $value) {
                if (!empty($value)) {
                    $filteredData = array_filter($filteredData, function($row) use ($column, $value) {
                        return isset($row[$column]) && trim($row[$column]) == trim($value);
                    });
                }
            }
        }
        
        // Apply search filter
        $searchTerm = $request->get('search');
        if (!empty($searchTerm)) {
            $filteredData = array_filter($filteredData, function($row) use ($searchTerm) {
                foreach ($row as $value) {
                    if (stripos($value, $searchTerm) !== false) {
                        return true;
                    }
                }
                return false;
            });
        }
        
        // Re-index array to avoid issues with array_slice
        return array_values($filteredData);
    }

    // Method untuk fix data yang sudah ada
    public function fixDataStructure($id)
    {
        try {
            $dataset = Dataset::findOrFail($id);
            
            if (empty($dataset->data)) {
                return response()->json(['error' => 'No data to fix']);
            }
            
            // Get original keys from first row
            $originalKeys = array_keys($dataset->data[0]);
            
            // Restructure data
            $fixedData = [];
            foreach ($dataset->data as $row) {
                $newRow = [];
                foreach ($dataset->headers as $index => $header) {
                    $originalKey = $originalKeys[$index] ?? null;
                    $value = $originalKey ? ($row[$originalKey] ?? null) : null;
                    $newRow[$header] = $value;
                }
                $fixedData[] = $newRow;
            }
            
            // Update dataset
            $dataset->update(['data' => $fixedData]);
            
            return response()->json([
                'success' => true,
                'message' => 'Data structure fixed successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $dataset = Dataset::findOrFail($id);
            
            // Delete file from storage if exists
            if (isset($dataset->file_path) && Storage::disk('public')->exists($dataset->file_path)) {
                Storage::disk('public')->delete($dataset->file_path);
            }
            
            $dataset->delete();
            
            return redirect()->route('dataset.index')
                ->with('success', 'Dataset berhasil dihapus!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // API endpoint untuk mendapatkan data dalam format JSON
    public function api($id)
    {
        $dataset = Dataset::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $dataset->id,
                'title' => $dataset->title ?? $dataset->filename,
                'description' => $dataset->description ?? '',
                'filename' => $dataset->filename,
                'headers' => $dataset->headers,
                'data' => $dataset->data,
                'total_rows' => $dataset->total_rows,
                'total_columns' => $dataset->total_columns ?? count($dataset->headers),
                'topic' => $dataset->topic ?? '',
                'classification' => $dataset->classification ?? '',
                'tags' => $dataset->tags ?? [],
                'created_at' => $dataset->created_at,
                'updated_at' => $dataset->updated_at
            ]
        ]);
    }

    // Download dataset file
    public function download($id)
    {
        $dataset = Dataset::findOrFail($id);

        if (!isset($dataset->file_path) || !Storage::disk('public')->exists($dataset->file_path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        // Increment download counter if field exists
        if (Schema::hasColumn('datasets', 'download_count')) {
            $dataset->increment('download_count');
        }

        $originalFilename = $dataset->original_filename ?? $dataset->filename;

        return Storage::disk('public')->download(
            $dataset->file_path,
            $originalFilename
        );
    }

    // Preview file before upload (AJAX)
    public function previewFile(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);

        $file = $request->file('file');
        $preview = $this->previewExcelData($file);

        return response()->json($preview);
    }

    // Helper method untuk process tags
    private function processTags($tags)
    {
        try {
            if (is_string($tags)) {
                $processedTags = array_filter(array_map('trim', explode(',', $tags)));
                Log::info('Processed tags: ' . json_encode($processedTags));
                return $processedTags;
            }
            return $tags;
        } catch (\Exception $e) {
            Log::error('Error processing tags: ' . $e->getMessage());
            return [];
        }
    }

    // Debug method untuk troubleshooting
    public function debugDataset($id)
    {
        $dataset = Dataset::findOrFail($id);
        
        return response()->json([
            'dataset_info' => [
                'id' => $dataset->id,
                'filename' => $dataset->filename,
                'headers_count' => count($dataset->headers ?? []),
                'data_count' => count($dataset->data ?? []),
                'total_rows' => $dataset->total_rows,
                'created_at' => $dataset->created_at,
            ],
            'headers' => $dataset->headers,
            'sample_data' => array_slice($dataset->data ?? [], 0, 2),
            'database_columns' => Schema::getColumnListing('datasets'),
        ]);
    }

    // Method untuk statistik (jika diperlukan)
    public function statistics()
    {
        $stats = [
            'total_datasets' => Dataset::count(),
            'total_rows' => Dataset::sum('total_rows'),
            'datasets_by_topic' => Dataset::selectRaw('topic, COUNT(*) as count')
                                         ->whereNotNull('topic')
                                         ->groupBy('topic')
                                         ->pluck('count', 'topic'),
            'datasets_by_classification' => Dataset::selectRaw('classification, COUNT(*) as count')
                                                  ->whereNotNull('classification')
                                                  ->groupBy('classification')
                                                  ->pluck('count', 'classification'),
        ];

        return view('dataset.statistics', compact('stats'));
    }

    // Batch operations
    public function batchDelete(Request $request)
    {
        $request->validate([
            'dataset_ids' => 'required|array',
            'dataset_ids.*' => 'exists:datasets,id'
        ]);

        $deletedCount = 0;

        foreach ($request->dataset_ids as $datasetId) {
            try {
                $dataset = Dataset::findOrFail($datasetId);
                
                // Delete file from storage if exists
                if (isset($dataset->file_path) && Storage::disk('public')->exists($dataset->file_path)) {
                    Storage::disk('public')->delete($dataset->file_path);
                }

                $dataset->delete();
                $deletedCount++;

            } catch (\Exception $e) {
                Log::error("Failed to delete dataset ID {$datasetId}: " . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', "Berhasil menghapus {$deletedCount} dataset.");
    }

    
// public function history(Request $request)
// {
//     // Ambil semua dataset milik user yang approval_status bukan 'approved'
//     $dataArray = Dataset::where('user_id', Auth::id())
//         ->where('approval_status', '!=', 'approved') // filter tambahan
//         ->with(['user'])
//         ->orderBy('updated_at', 'desc')
//         ->get()
//         ->toArray();

//     // Apply filter
//     $filteredData = $this->applyFilters($dataArray, request());

//     // Ubah array hasil filter menjadi Collection of Dataset model lagi
//     $filteredCollection = collect($filteredData)->map(function ($item) {
//         return (new Dataset)->forceFill($item);
//     });

//     // Pagination manual
//     $perPage = 15;
//     $page = request()->get('page', 1);
//     $datasets = new \Illuminate\Pagination\LengthAwarePaginator(
//         $filteredCollection->forPage($page, $perPage),
//         $filteredCollection->count(),
//         $perPage,
//         $page,
//         [
//             'path' => request()->url(),
//             'query' => request()->query()
//         ]
//     );

//     return view('dataset.history', compact('datasets'));
// }


public function history(Request $request)
{
    // Get base query with necessary relationships
    $query = Dataset::where('user_id', Auth::id())
        ->where('approval_status', '!=', 'approved')
        ->with(['user']);

    // Apply filters directly to the query instead of after fetching all data
    $query = $this->applyFiltersToQuery($query, $request);

    // Get paginated results
    $datasets = $query->orderBy('updated_at', 'desc')
        ->paginate(15)
        ->appends($request->query());

    return view('dataset.history', compact('datasets'));
}

/**
 * Apply filters directly to the database query for better performance
 */
private function applyFiltersToQuery($query, Request $request)
{
    // Example filter implementations
    if ($request->has('approval_status') && $request->approval_status !== '') {
        $query->where('approval_status', $request->approval_status);
    }

    if ($request->has('search') && $request->search !== '') {
        $searchTerm = $request->search;
        $query->where(function ($q) use ($searchTerm) {
            $q->where('title', 'LIKE', "%{$searchTerm}%")
              ->orWhere('description', 'LIKE', "%{$searchTerm}%")
              ->orWhere('tags', 'LIKE', "%{$searchTerm}%");
        });
    }

    // Add date range filter example
    if ($request->has('date_from') && $request->date_from !== '') {
        $query->whereDate('created_at', '>=', $request->date_from);
    }

    if ($request->has('date_to') && $request->date_to !== '') {
        $query->whereDate('created_at', '<=', $request->date_to);
    }

    return $query;
}

/**
 * Alternative: If you need to keep the existing applyFilters method for array processing
 */
public function historyWithArrayFiltering(Request $request)
{
    try {
        // Get data with optimized query
        $dataArray = Dataset::where('user_id', Auth::id())
            ->where('approval_status', '!=', 'approved')
            ->with(['user'])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->toArray();

        // Apply existing filter method
        $filteredData = $this->applyFilters($dataArray, $request);

        // More efficient way to convert back to models
        $filteredCollection = Dataset::hydrate($filteredData);

        // Manual pagination with better error handling
        $perPage = (int) $request->get('per_page', 15);
        $page = (int) $request->get('page', 1);
        
        $datasets = new \Illuminate\Pagination\LengthAwarePaginator(
            $filteredCollection->forPage($page, $perPage)->values(),
            $filteredCollection->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
                'pageName' => 'page'
            ]
        );

        return view('dataset.history', compact('datasets'));
    } catch (\Exception $e) {
        // Log error and return with error message
        return back()->with('error', 'Unable to load dataset history.');
    }
}

/**
 * Optimized version using Laravel's built-in filtering
 */
public function historyOptimized(Request $request)
{
    $datasets = Dataset::where('user_id', Auth::id())
        ->where('approval_status', '!=', 'approved')
        ->with(['user'])
        ->when($request->approval_status, function ($query, $status) {
            return $query->where('approval_status', $status);
        })
        ->when($request->search, function ($query, $search) {
            return $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('tags', 'LIKE', "%{$search}%");
            });
        })
        ->orderBy('updated_at', 'desc')
        ->paginate(15)
        ->appends($request->query());

    return view('dataset.history', compact('datasets'));
}

}