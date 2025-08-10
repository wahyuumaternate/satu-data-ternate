<?php

namespace App\Http\Controllers;

use App\Models\Dataset;
use App\Imports\DynamicImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DatasetController extends Controller
{
    public function index()
    {
        $datasets = Dataset::orderBy('created_at', 'desc')->paginate(10);
        return view('dataset.index', compact('datasets'));
    }

    public function create()
    {
        return view('dataset.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            DB::beginTransaction();
            
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Log file info
            Log::info('Importing file: ' . $filename);
            Log::info('File size: ' . $file->getSize() . ' bytes');
            Log::info('File extension: ' . $file->getClientOriginalExtension());
            
            // Preview data untuk debugging (optional)
            if ($request->has('preview')) {
                $preview = $this->previewExcelData($file);
                return response()->json($preview);
            }
            
            Excel::import(new DynamicImport($filename), $file);
            
            DB::commit();
            
            return redirect()->route('dataset.index')
                ->with('success', 'Data berhasil diimport dari file: ' . $file->getClientOriginalName());
                
        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Import failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

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

     public function show($id, Request $request)
    {
        $dataset = Dataset::findOrFail($id);
        
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

    // public function show($id, Request $request)
    // {
    //     $dataset = Dataset::findOrFail($id);
        
    //     $page = $request->get('page', 1);
    //     $perPage = $request->get('per_page', 10);
        
    //     $paginatedData = $dataset->getPaginatedData($perPage, $page);
        
    //     return view('dataset.show', compact('dataset', 'paginatedData'));
    // }

    public function destroy($id)
    {
        try {
            $dataset = Dataset::findOrFail($id);
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
                'filename' => $dataset->filename,
                'headers' => $dataset->headers,
                'data' => $dataset->data,
                'total_rows' => $dataset->total_rows,
                'created_at' => $dataset->created_at,
                'updated_at' => $dataset->updated_at
            ]
        ]);
    }
}