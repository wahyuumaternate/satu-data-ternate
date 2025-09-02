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
    private function checkViewPermission()
    {
        $user = Auth::user();
        
        if ($user->hasRole('reviewer')) {
            abort(403, 'Reviewer tidak memiliki akses ke dataset management. Silakan gunakan menu approval.');
        }
    }

    private function checkCreatePermission()
    {
        $user = Auth::user();
        
        if ($user->hasRole('reviewer')) {
            abort(403, 'Reviewer tidak memiliki akses ke dataset management.');
        }
        
        if ($user->hasRole('penanggung-jawab')) {
            abort(403, 'Penanggung jawab hanya dapat melihat dan mendownload dataset.');
        }
        
        if (!$user->hasRole(['super-admin', 'opd'])) {
            abort(403, 'Hanya Super Admin dan OPD yang dapat membuat dataset.');
        }
    }

    private function checkEditPermission()
    {
        $user = Auth::user();
        
        if ($user->hasRole('reviewer')) {
            abort(403, 'Reviewer tidak memiliki akses ke dataset management.');
        }
        
        if ($user->hasRole('penanggung-jawab')) {
            abort(403, 'Penanggung jawab hanya dapat melihat dan mendownload dataset.');
        }
        
        if (!$user->hasRole(['super-admin', 'opd'])) {
            abort(403, 'Hanya Super Admin dan OPD yang dapat mengedit dataset.');
        }
    }

    private function checkDatasetAccess($dataset)
    {
        $user = Auth::user();
        
        // Super admin bisa akses semua
        if ($user->hasRole('super-admin')) {
            return;
        }
        
        // OPD hanya bisa akses data miliknya
        if ($user->hasRole('opd')) {
            if ($dataset->user_id !== $user->id) {
                abort(403, 'Anda hanya dapat mengakses dataset milik Anda sendiri.');
            }
            return;
        }
        
        // Penanggung jawab hanya bisa akses data yang approved
        if ($user->hasRole('penanggung-jawab')) {
            if ($dataset->approval_status !== 'approved') {
                abort(403, 'Anda hanya dapat mengakses dataset yang sudah disetujui.');
            }
            return;
        }
    }

    public function index(Request $request)
    {
        $this->checkViewPermission();
        
        $user = Auth::user();
        $query = Dataset::with(['user', 'approvedBy']);
        
        // Role-based filtering untuk data yang ditampilkan
        if ($user->hasRole('super-admin')) {
            // Super admin bisa melihat semua data
            // Bisa toggle antara approved atau semua status
            if (!$request->has('show_all')) {
                $query->where('approval_status', 'approved');
            }
        } elseif ($user->hasRole('opd')) {
            // OPD hanya bisa melihat data miliknya sendiri
            $query->where('user_id', $user->id);
        } elseif ($user->hasRole('penanggung-jawab')) {
            // Penanggung jawab bisa melihat semua data yang approved
            $query->where('approval_status', 'approved');
        }

        // Apply other filters (search, topic, etc.)
        $this->applyCommonFilters($query, $request);

        // Paginate results
        $datasets = $query->paginate(12)->withQueryString();

        // Process tags untuk setiap dataset
        $datasets->getCollection()->transform(function ($dataset) {
            if (is_string($dataset->tags)) {
                $dataset->tags = json_decode($dataset->tags, true) ?? [];
            }
            if (!is_array($dataset->tags)) {
                $dataset->tags = [];
            }
            return $dataset;
        });

        // Get statistics berdasarkan role
        $stats = $this->getStatsBasedOnRole($user);

        // Get filter options
        $filterOptions = $this->getFilterOptions($user);

        $currentFilter = $request->approval_status;
        $pageTitle = $this->getPageTitle($user, $currentFilter);

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
        $this->checkCreatePermission();

        return view('dataset.create');
    }

    public function store(Request $request)
    {
        $this->checkCreatePermission();

        // Validasi untuk input spreadsheet
        $request->validate([
            'spreadsheet_data' => 'required|string',
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
            'spreadsheet_data.required' => 'Data spreadsheet harus diisi.',
            'title.unique' => 'Judul dataset sudah digunakan, silakan pilih judul lain.',
        ]);

        try {
            DB::beginTransaction();
            
            $user = Auth::user();
            
            // Parse spreadsheet data
            $spreadsheetData = json_decode($request->spreadsheet_data, true);
            
            if (!$spreadsheetData || !is_array($spreadsheetData)) {
                throw new \Exception('Data spreadsheet tidak valid.');
            }

            // Process spreadsheet data
            $processedData = $this->processSpreadsheetData($spreadsheetData);
            
            $publishStatus = 'published';
            if ($request->has('action') && $request->action === 'draft') {
                $publishStatus = 'draft';
            }

            // Generate slug from title
            $baseSlug = Str::slug($request->title);
            $slug = $baseSlug;
            $counter = 1;
            
            while (Dataset::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            // Create filename based on title
            $filename = Str::slug($request->title) . '_' . time() . '.json';

            $dataset = Dataset::create([
                'title' => $request->title,
                'slug' => $slug,
                'description' => $request->description,
                'tags' => $this->processTags($request->tags),
                'filename' => $filename,
                'original_filename' => $request->title . '.json',
                'file_path' => null, // No actual file stored
                'file_size' => strlen(json_encode($processedData['data'])),
                'file_type' => 'spreadsheet',
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
                'user_id' => $user->id,
                'organization' => $user->organization->name ?? '',
                'publish_status' => $publishStatus,
                'approval_status' => 'pending', // Default pending approval
                'headers' => $processedData['headers'],
                'data' => $processedData['data'],
                'total_rows' => $processedData['total_rows'],
                'total_columns' => $processedData['total_columns']
            ]);

            Log::info('Dataset record created with ID: ' . $dataset->id . ' from spreadsheet input');
            
            DB::commit();
            
            if ($request->has('action') && $request->action === 'draft') {
                return redirect()->route('dataset.index')
                    ->with('success', 'Dataset berhasil disimpan sebagai draft: ' . $request->title);
            }

            return redirect()->route('dataset.show', $slug)
                ->with('success', 'Dataset berhasil dibuat dan menunggu persetujuan: ' . $request->title);
                
        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Dataset store failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Process spreadsheet data from frontend
     */
    private function processSpreadsheetData($spreadsheetData)
    {
        try {
            // Filter out empty rows
            $filteredData = array_filter($spreadsheetData, function($row) {
                return array_filter($row, function($cell) {
                    return !empty(trim($cell));
                });
            });

            if (empty($filteredData)) {
                throw new \Exception('Tidak ada data yang valid dalam spreadsheet.');
            }

            // Get first row as headers
            $headers = array_shift($filteredData);
            
            // Clean headers - remove empty headers and generate column names
            $cleanHeaders = [];
            foreach ($headers as $index => $header) {
                $cleanHeader = trim($header);
                if (empty($cleanHeader)) {
                    $cleanHeader = 'Column_' . ($index + 1);
                }
                $cleanHeaders[] = $cleanHeader;
            }

            // Process data rows
            $processedData = [];
            foreach ($filteredData as $rowData) {
                $row = [];
                foreach ($cleanHeaders as $index => $header) {
                    $row[$header] = isset($rowData[$index]) ? trim($rowData[$index]) : '';
                }
                $processedData[] = $row;
            }

            return [
                'headers' => $cleanHeaders,
                'data' => $processedData,
                'total_rows' => count($processedData),
                'total_columns' => count($cleanHeaders)
            ];

        } catch (\Exception $e) {
            Log::error('Error processing spreadsheet data: ' . $e->getMessage());
            throw new \Exception('Gagal memproses data spreadsheet: ' . $e->getMessage());
        }
    }

    public function show($slug, Request $request)
    {
        $this->checkViewPermission();
        
        $dataset = Dataset::where('slug', $slug)->firstOrFail();
        $this->checkDatasetAccess($dataset);
        
        // Increment view count if field exists
        if (Schema::hasColumn('datasets', 'view_count')) {
            $dataset->increment('view_count');
        }
        
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 10);
        
        $filteredData = $this->applyFilters($dataset->data, $request);
        
        $total = count($filteredData);
        $offset = ($page - 1) * $perPage;
        
        $paginatedData = [
            'data' => array_slice($filteredData, $offset, $perPage),
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
        
        $user = Auth::user();
        if ($request->has('debug') && $user->hasRole('super-admin')) {
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

    public function edit($slug)
    {
        $this->checkEditPermission();
        
        $dataset = Dataset::where('slug', $slug)->firstOrFail();
        $this->checkDatasetAccess($dataset);

        return view('dataset.edit', compact('dataset'));
    }

    public function update(Request $request, $id)
    {
        $this->checkEditPermission();
        
        $dataset = Dataset::findOrFail($id);
        $this->checkDatasetAccess($dataset);

        $request->validate([
            'spreadsheet_data' => 'nullable|string',
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
            
            // Check if approval status should be reset to pending
            $shouldResetApproval = false;
            
            // Reset approval to pending if current status is rejected
            if ($dataset->approval_status === 'rejected') {
                $shouldResetApproval = true;
            }
            
            // Handle spreadsheet data update
            if ($request->has('spreadsheet_data') && !empty($request->spreadsheet_data)) {
                $spreadsheetData = json_decode($request->spreadsheet_data, true);
                
                if (!$spreadsheetData || !is_array($spreadsheetData)) {
                    throw new \Exception('Data spreadsheet tidak valid.');
                }

                $processedData = $this->processSpreadsheetData($spreadsheetData);
                
                $dataset->headers = $processedData['headers'];
                $dataset->data = $processedData['data'];
                $dataset->total_rows = $processedData['total_rows'];
                $dataset->total_columns = $processedData['total_columns'];
                $dataset->file_size = strlen(json_encode($processedData['data']));
                
                $shouldResetApproval = true; // Reset approval when data is updated
            }

            // Update slug if title changed
            if ($dataset->title !== $request->title) {
                $baseSlug = Str::slug($request->title);
                $slug = $baseSlug;
                $counter = 1;
                
                while (Dataset::where('slug', $slug)->where('id', '!=', $dataset->id)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
                
                $dataset->slug = $slug;
            }
            
            // Prepare update data
            $updateData = [
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
            ];
            
            // Add approval status reset if needed
            if ($shouldResetApproval) {
                $updateData['approval_status'] = 'pending';
            }
            
            $dataset->update($updateData);

            $successMessage = 'Dataset berhasil diupdate.' .
                ($shouldResetApproval ? ' Status approval direset ke pending untuk review ulang.' : '');
            
            DB::commit();
            
            return redirect()->route('dataset.show', $dataset->slug)
                ->with('success', $successMessage);
                
        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Dataset update failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengupdate dataset: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($slug)
    {
        $this->checkEditPermission();
        
        $dataset = Dataset::where('slug', $slug)->firstOrFail();
        $this->checkDatasetAccess($dataset);

        try {
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

    public function download($slug)
    {
        $this->checkViewPermission();
        
        $dataset = Dataset::where('slug', $slug)->firstOrFail();
        $this->checkDatasetAccess($dataset);

        if (Schema::hasColumn('datasets', 'download_count')) {
            $dataset->increment('download_count');
        }

        // For spreadsheet-based datasets, generate CSV download
        if ($dataset->file_type === 'spreadsheet') {
            return $this->generateCsvDownload($dataset);
        }

        // For file-based datasets, download the original file
        if (!isset($dataset->file_path) || !Storage::disk('public')->exists($dataset->file_path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        $originalFilename = $dataset->original_filename ?? $dataset->filename;

        return Storage::disk('public')->download(
            $dataset->file_path,
            $originalFilename
        );
    }

    /**
     * Generate CSV download for spreadsheet-based datasets
     */
    private function generateCsvDownload($dataset)
    {
        $filename = Str::slug($dataset->title) . '_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($dataset) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            if (!empty($dataset->headers)) {
                fputcsv($file, $dataset->headers);
            }
            
            // Add data rows
            foreach ($dataset->data as $row) {
                fputcsv($file, array_values($row));
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // API endpoint dengan permission check
    public function api($id)
    {
        $this->checkViewPermission();
        
        $dataset = Dataset::findOrFail($id);
        $this->checkDatasetAccess($dataset);
        
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

    // History method - hanya untuk OPD dan Super Admin
    public function history(Request $request)
    {
        $user = Auth::user();
        
        // Check permission - reviewer tidak bisa akses
        if ($user->hasRole('reviewer')) {
            abort(403, 'Reviewer tidak memiliki akses ke dataset management.');
        }
        
        // Hanya OPD dan Super Admin yang bisa lihat history
        if (!$user->hasRole(['super-admin', 'opd'])) {
            abort(403, 'Anda tidak memiliki akses untuk melihat riwayat dataset.');
        }

        $query = Dataset::with(['user']);

        if ($user->hasRole('opd')) {
            $query->where('user_id', $user->id);
        }
        
        // Show non-approved datasets for history
        $query->where('approval_status', '!=', 'approved');

        // Apply filters
        $query = $this->applyFiltersToQuery($query, $request);

        $datasets = $query->orderBy('updated_at', 'desc')
            ->paginate(15)
            ->appends($request->query());

        return view('dataset.history', compact('datasets'));
    }

    // Helper methods for stats and filters based on role
    private function getStatsBasedOnRole($user)
    {
        $statsQuery = Dataset::query();
        
        if ($user->hasRole('opd')) {
            $statsQuery->where('user_id', $user->id);
        } elseif ($user->hasRole('penanggung-jawab')) {
            $statsQuery->where('approval_status', 'approved');
        }
        
        return [
            'total_datasets' => (clone $statsQuery)->count(),
            'pending_approval' => (clone $statsQuery)->where('approval_status', 'pending')->count(),
            'approved_datasets' => (clone $statsQuery)->where('approval_status', 'approved')->count(),
            'rejected_datasets' => (clone $statsQuery)->where('approval_status', 'rejected')->count(),
            'published_datasets' => (clone $statsQuery)->where('publish_status', 'published')->count(),
            'draft_datasets' => (clone $statsQuery)->where('publish_status', 'draft')->count(),
            'total_views' => (clone $statsQuery)->sum('view_count'),
            'total_downloads' => (clone $statsQuery)->sum('download_count'),
        ];
    }

    private function getFilterOptions($user)
    {
        $filterQuery = Dataset::query();
        
        if ($user->hasRole('opd')) {
            $filterQuery->where('user_id', $user->id);
        } elseif ($user->hasRole('penanggung-jawab')) {
            $filterQuery->where('approval_status', 'approved');
        }

        return [
            'topics' => (clone $filterQuery)->distinct()->pluck('topic')->filter()->sort()->values(),
            'classifications' => (clone $filterQuery)->distinct()->pluck('classification')->filter()->sort()->values(),
            'organizations' => (clone $filterQuery)->distinct()->pluck('organization')->filter()->sort()->values(),
            'file_types' => (clone $filterQuery)->distinct()->pluck('file_type')->filter()->sort()->values(),
            'sectors' => (clone $filterQuery)->distinct()->pluck('sector')->filter()->sort()->values(),
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
    }

    private function getPageTitle($user, $currentFilter)
    {
        $baseTitle = 'All Datasets';
        
        if ($user->hasRole('opd')) {
            $baseTitle = 'My Datasets';
        } elseif ($user->hasRole('penanggung-jawab')) {
            $baseTitle = 'Approved Datasets';
        }
        
        switch ($currentFilter) {
            case 'pending':
                return $user->hasRole('super-admin') ? 'Pending Datasets' : 'My Pending Datasets';
            case 'approved':
                return $user->hasRole('super-admin') ? 'Approved Datasets' : 'My Approved Datasets';
            case 'rejected':
                return $user->hasRole('super-admin') ? 'Rejected Datasets' : 'My Rejected Datasets';
            default:
                return $baseTitle;
        }
    }

    // Apply common filters method
    private function applyCommonFilters($query, $request)
    {
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
                  ->orWhereJsonContains('tags', $search)
                  ->orWhere('tags', 'like', "%{$search}%");
            });
        }

        if ($request->has('topic') && $request->topic) {
            $query->where('topic', $request->topic);
        }

        if ($request->has('classification') && $request->classification) {
            $query->where('classification', $request->classification);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('approval_status') && $request->approval_status) {
            $query->where('approval_status', $request->approval_status);
            
            if (in_array($request->approval_status, ['approved', 'rejected'])) {
                $query->orderBy('approved_at', 'desc');
            }
        }

        // Add other filters as needed...
        
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        $allowedSortColumns = [
            'created_at', 'title', 'view_count', 'download_count', 
            'total_rows', 'total_columns', 'file_size', 'updated_at'
        ];

        if (in_array($sortBy, $allowedSortColumns)) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('created_at', 'desc');
        }
    }

    private function applyFiltersToQuery($query, Request $request)
    {
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

        if ($request->has('date_from') && $request->date_from !== '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to !== '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return $query;
    }

    // Keep existing methods for file processing, etc.
    private function applyFilters(array $data, Request $request): array
    {
        $filteredData = $data;
        
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
        
        return array_values($filteredData);
    }

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
}