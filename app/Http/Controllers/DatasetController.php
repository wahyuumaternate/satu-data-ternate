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
        $query = Dataset::orderBy('created_at', 'desc');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('filename', 'like', "%{$search}%");
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
            $query->where('publish_status', $request->status);
        }

        $datasets = $query->paginate(12);
        return view('dataset.index', compact('datasets'));
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
            
            return redirect()->route('dataset.show', $dataset)
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
        $dataset = Dataset::findOrFail($id);
        return view('dataset.edit', compact('dataset'));
    }

    public function update(Request $request, $id)
    {
        $dataset = Dataset::findOrFail($id);

        $request->validate([
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
        ]);

        try {
            $dataset->update([
                'title' => $request->title,
                'description' => $request->description,
                'tags' => $this->processTags($request->tags),
                'topic' => $request->topic,
                'classification' => $request->classification,
                'status' => $request->status,
                'license' => $request->license,
                'sector' => $request->sector,
                'responsible_person' => $request->responsible_person,
                'contact' => $request->contact,
                'data_source' => $request->data_source,
                'data_period' => $request->data_period,
                'update_frequency' => $request->update_frequency,
                'geographic_coverage' => $request->geographic_coverage,
            ]);

            return redirect()->route('dataset.show', $dataset)
                ->with('success', 'Dataset berhasil diperbarui.');

        } catch (\Exception $e) {
            Log::error('Dataset update failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
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
}