<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use App\Models\Infografis;
use App\Models\Mapset;
use App\Models\Visualisasi;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use App\Exports\DatasetExport;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class DatasetApiController extends Controller
{
    /**
     * Default pagination limit
     */
    protected int $defaultLimit = 100;
    protected int $maxLimit = 100;

    /**
     * Get list of datasets with pagination and filters
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Dataset::where('approval_status', 'approved')
                ->where('classification', 'publik') // hanya dataset public
                ->with(['user.organization:id,name']);

            // Filter umum (misal pakai helper kalau perlu)
            $this->applyCommonFilters($query, $request);

            // Sorting (default created_at desc)
            $allowedSort = ['created_at', 'title', 'view_count', 'download_count', 'total_rows', 'total_columns', 'file_size'];
            $sortBy = $request->get('sort_by', 'created_at');
            $sortDir = $request->get('sort_direction', 'desc');

            $query->orderBy(in_array($sortBy, $allowedSort) ? $sortBy : 'created_at', $sortDir);

            // Pagination
            $perPage = min($request->get('per_page', $this->defaultLimit), $this->maxLimit);
            $datasets = $query->paginate($perPage);

            $datasets->getCollection()->transform(function ($dataset) {
                // Format tags
                $dataset->tags = is_string($dataset->tags) ? json_decode($dataset->tags, true) ?? [] : (is_array($dataset->tags) ? $dataset->tags : []);

                // Tambahkan format ukuran file
                $dataset->file_size_formatted = $this->formatFileSize($dataset->file_size);

                // Hapus field sensitif
                unset($dataset->file_path, $dataset->headers, $dataset->data);

                // Tambahkan nama organisasi
                $dataset->organization_name = $dataset->user->organization->name ?? null;

                return $dataset;
            });

            return $this->successResponse(
                [
                    'datasets' => $datasets->items(),
                    'pagination' => $this->getPaginationMeta($datasets),
                ],
                'Datasets retrieved successfully',
            );
        } catch (\Exception $e) {
            Log::error('Dataset index API error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve datasets', 500);
        }
    }

    /**
     * Get single dataset details
     */
    public function show(string $slug): JsonResponse
    {
        try {
            // Cari dataset dengan kondisi yang lebih specific
            $dataset = Dataset::where('slug', $slug)
                ->where('approval_status', 'approved')
                ->where('is_public', true)
                ->where('publish_status', 'published') // Pastikan published
                ->with(['user.organization:id,name'])
                ->first();

            // Debug: Log jika dataset tidak ditemukan
            if (!$dataset) {
                Log::warning('Dataset not found', [
                    'slug' => $slug,
                    'criteria' => [
                        'approval_status' => 'approved',
                        'is_public' => true,
                        'publish_status' => 'published',
                    ],
                ]);

                // Cek apakah dataset exists dengan slug tapi tidak memenuhi kriteria
                $existingDataset = Dataset::where('slug', $slug)->first();
                if ($existingDataset) {
                    Log::info('Dataset exists but does not meet criteria', [
                        'slug' => $slug,
                        'approval_status' => $existingDataset->approval_status,
                        'is_public' => $existingDataset->is_public,
                        'publish_status' => $existingDataset->publish_status ?? 'null',
                    ]);
                }

                return $this->errorResponse('Dataset not found or not publicly available', 404);
            }

            Log::info('Dataset found', ['dataset_id' => $dataset->id]);

            // Tambah view count dengan error handling
            try {
                if (Schema::hasColumn('datasets', 'view_count')) {
                    $dataset->increment('view_count');
                    Log::info('View count incremented', ['dataset_id' => $dataset->id]);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to increment view count', [
                    'dataset_id' => $dataset->id,
                    'error' => $e->getMessage(),
                ]);
                // Continue execution even if view count fails
            }

            // Format tags dengan error handling
            try {
                if (is_string($dataset->tags)) {
                    $decodedTags = json_decode($dataset->tags, true);
                    $dataset->tags = $decodedTags !== null ? $decodedTags : [];
                } elseif (!is_array($dataset->tags)) {
                    $dataset->tags = [];
                }
            } catch (\Exception $e) {
                Log::warning('Failed to format tags', [
                    'dataset_id' => $dataset->id,
                    'tags' => $dataset->tags,
                    'error' => $e->getMessage(),
                ]);
                $dataset->tags = [];
            }

            // Format file size dengan error handling
            try {
                $dataset->file_size_formatted = $this->formatFileSize($dataset->file_size ?? 0);
            } catch (\Exception $e) {
                Log::warning('Failed to format file size', [
                    'dataset_id' => $dataset->id,
                    'file_size' => $dataset->file_size,
                    'error' => $e->getMessage(),
                ]);
                $dataset->file_size_formatted = '0 B';
            }

            // Tambahkan nama organisasi dengan error handling
            try {
                $dataset->organization_name = $dataset->user->organization->name ?? 'Unknown Organization';
            } catch (\Exception $e) {
                Log::warning('Failed to get organization name', [
                    'dataset_id' => $dataset->id,
                    'error' => $e->getMessage(),
                ]);
                $dataset->organization_name = 'Unknown Organization';
            }

            // Ambil headers dan sample data jika file CSV/Excel
            $headers = [];
            $sampleData = [];

            try {
                if (in_array(strtolower($dataset->file_type), ['csv', 'excel', 'xlsx', 'xls'])) {
                    $filePath = storage_path('app/datasets/' . $dataset->filename);

                    if (file_exists($filePath)) {
                        if (strtolower($dataset->file_type) === 'csv') {
                            // Baca CSV headers dan sample data
                            $handle = fopen($filePath, 'r');
                            if ($handle) {
                                // Ambil headers (baris pertama)
                                $headers = fgetcsv($handle);

                                // Ambil 5 baris sample data
                                $rowCount = 0;
                                while (($row = fgetcsv($handle)) !== false && $rowCount < 5) {
                                    $sampleData[] = $row;
                                    $rowCount++;
                                }
                                fclose($handle);
                            }
                        } elseif (in_array(strtolower($dataset->file_type), ['xlsx', 'xls', 'excel'])) {
                            // Untuk Excel, butuh library PhpSpreadsheet
                            // Implementasi basic jika ada
                            $headers = ['Column 1', 'Column 2', 'Column 3']; // Placeholder
                            $sampleData = [['Sample', 'Data', 'Row']]; // Placeholder
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Failed to read file headers/data', [
                    'dataset_id' => $dataset->id,
                    'file_type' => $dataset->file_type,
                    'error' => $e->getMessage(),
                ]);
                // Continue with empty headers/data
            }

            // Tambahkan headers dan sample data ke dataset
            $dataset->headers = $headers;
            $dataset->sample_data = $sampleData;
            $dataset->has_preview = !empty($headers);

            // Hapus field sensitif yang ada (jangan hapus headers dan data karena kita baru tambahkan)
            $fieldsToRemove = ['file_path', 'processing_log'];
            foreach ($fieldsToRemove as $field) {
                if (property_exists($dataset, $field)) {
                    unset($dataset->$field);
                }
            }

            // Build response dengan error handling
            $response = [
                'dataset' => $dataset,
                'statistics' => [
                    'views' => $dataset->view_count ?? 0,
                    'downloads' => $dataset->download_count ?? 0,
                    'total_rows' => $dataset->total_rows ?? 0,
                    'total_columns' => $dataset->total_columns ?? 0,
                    'file_size' => $dataset->file_size ?? 0,
                ],
                'preview' => [
                    'headers' => $headers,
                    'sample_data' => $sampleData,
                    'has_preview' => !empty($headers),
                    'preview_rows' => count($sampleData),
                ],
            ];

            // Add download URL with error handling
            try {
                $response['download_url'] = route('api.v1.datasets.download', $slug);
            } catch (\Exception $e) {
                Log::warning('Failed to generate download URL', [
                    'slug' => $slug,
                    'error' => $e->getMessage(),
                ]);
                // Don't include download URL if route doesn't exist
            }

            Log::info('Dataset response prepared successfully', ['dataset_id' => $dataset->id]);

            return $this->successResponse($response, 'Dataset retrieved successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Dataset not found exception', [
                'slug' => $slug,
                'error' => $e->getMessage(),
            ]);
            return $this->errorResponse('Dataset not found', 404);
        } catch (\Exception $e) {
            Log::error('Dataset show API error', [
                'slug' => $slug,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->errorResponse('Failed to retrieve dataset: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Debug method to check dataset existence
     */
    public function debug(string $slug): JsonResponse
    {
        try {
            // Check if dataset exists with slug
            $dataset = Dataset::where('slug', $slug)->first();

            if (!$dataset) {
                return $this->errorResponse('No dataset found with this slug', 404);
            }

            // Return dataset info for debugging
            return $this->successResponse(
                [
                    'dataset_exists' => true,
                    'dataset_info' => [
                        'id' => $dataset->id,
                        'title' => $dataset->title,
                        'slug' => $dataset->slug,
                        'approval_status' => $dataset->approval_status,
                        'is_public' => $dataset->is_public,
                        'publish_status' => $dataset->publish_status ?? 'null',
                        'created_at' => $dataset->created_at,
                        'updated_at' => $dataset->updated_at,
                    ],
                ],
                'Dataset debug info',
            );
        } catch (\Exception $e) {
            Log::error('Debug API error', [
                'slug' => $slug,
                'error' => $e->getMessage(),
            ]);
            return $this->errorResponse('Debug failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get dataset data/content with pagination (like backend show method)
     */
    public function getData(string $slug, Request $request): JsonResponse
    {
        try {
            $dataset = Dataset::where('slug', $slug)->where('approval_status', 'approved')->firstOrFail();

            $page = $request->get('page', 1);
            $perPage = min($request->get('per_page', 50), 500);

            // Apply filters like in backend controller
            $filteredData = $this->applyDataFilters($dataset->data ?? [], $request);

            $total = count($filteredData);
            $offset = ($page - 1) * $perPage;

            $paginatedData = [
                'headers' => $dataset->headers ?? [],
                'data' => array_slice($filteredData, $offset, $perPage),
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'last_page' => ceil($total / $perPage),
                    'from' => $offset + 1,
                    'to' => min($offset + $perPage, $total),
                ],
                'filters_applied' => [
                    'search' => $request->get('search'),
                    'column_filters' => $request->get('filter', []),
                ],
            ];

            return $this->successResponse($paginatedData, 'Dataset data retrieved successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse('Dataset not found', 404);
        } catch (\Exception $e) {
            Log::error('Dataset getData API error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve dataset data', 500);
        }
    }

    /**
     * Download dataset as Excel/CSV
     *
     * @param string $slug
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download(string $slug)
    {
        try {
            // Find approved dataset
            $dataset = Dataset::where('slug', $slug)->where('approval_status', 'approved')->firstOrFail();

            // Validate data exists
            if (!$dataset->data || !is_array($dataset->data) || empty($dataset->data)) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No data available for download',
                    ],
                    404,
                );
            }

            // Increment download count
            if (Schema::hasColumn('datasets', 'download_count')) {
                $dataset->increment('download_count');
            }

            // Create filename
            $filename = $dataset->slug . '.xlsx';

            // Export using Laravel Excel
            return Excel::download(new DatasetExport($dataset->data), $filename, \Maatwebsite\Excel\Excel::XLSX);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Dataset not found',
                ],
                404,
            );
        } catch (\Exception $e) {
            Log::error('Dataset download API error: ' . $e->getMessage(), [
                'slug' => $slug,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Download failed: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Optional: Download as CSV
     */
    public function downloadCsv(string $slug)
    {
        try {
            $dataset = Dataset::where('slug', $slug)->where('approval_status', 'approved')->firstOrFail();

            if (!$dataset->data || !is_array($dataset->data) || empty($dataset->data)) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No data available for download',
                    ],
                    404,
                );
            }

            if (Schema::hasColumn('datasets', 'download_count')) {
                $dataset->increment('download_count');
            }

            $filename = $dataset->slug . '.csv';

            return Excel::download(new DatasetExport($dataset->data), $filename, \Maatwebsite\Excel\Excel::CSV);
        } catch (\Exception $e) {
            Log::error('Dataset CSV download error: ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Download failed',
                ],
                500,
            );
        }
    }

    /**
     * Get popular datasets
     */
    public function getPopular(Request $request): JsonResponse
    {
        $limit = min($request->get('limit', 10), 50);

        try {
            $datasets = Dataset::where('approval_status', 'approved')
                ->with(['user'])
                ->orderBy('view_count', 'desc')
                ->orderBy('download_count', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($dataset) {
                    if (is_string($dataset->tags)) {
                        $dataset->tags = json_decode($dataset->tags, true) ?? [];
                    }
                    if (!is_array($dataset->tags)) {
                        $dataset->tags = [];
                    }
                    $dataset->file_size_formatted = $this->formatFileSize($dataset->file_size);
                    unset($dataset->file_path);
                    return $dataset;
                });

            return $this->successResponse($datasets, 'Popular datasets retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Dataset getPopular API error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve popular datasets', 500);
        }
    }

    /**
     * Get recent datasets
     */
    public function getRecent(Request $request): JsonResponse
    {
        $limit = min($request->get('limit', 10), 50);

        try {
            $datasets = Dataset::where('approval_status', 'approved')
                ->with(['user'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($dataset) {
                    if (is_string($dataset->tags)) {
                        $dataset->tags = json_decode($dataset->tags, true) ?? [];
                    }
                    if (!is_array($dataset->tags)) {
                        $dataset->tags = [];
                    }
                    $dataset->file_size_formatted = $this->formatFileSize($dataset->file_size);
                    unset($dataset->file_path);
                    return $dataset;
                });

            return $this->successResponse($datasets, 'Recent datasets retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Dataset getRecent API error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve recent datasets', 500);
        }
    }

    /**
     * Get available topics (like backend getFilterOptions)
     */
    public function getTopics(): JsonResponse
    {
        try {
            $topics = Cache::remember('dataset_topics', 3600, function () {
                return Dataset::where('approval_status', 'approved')->distinct()->pluck('topic')->filter()->sort()->values();
            });

            return $this->successResponse($topics, 'Topics retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Dataset getTopics API error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve topics', 500);
        }
    }

    /**
     * Get datasets by topic
     */
    public function getByTopic(Request $request, string $topic): JsonResponse
    {
        $perPage = min($request->get('per_page', $this->defaultLimit), $this->maxLimit);

        try {
            $datasets = Dataset::where('approval_status', 'approved')
                ->where('topic', $topic)
                ->with(['user'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            $datasets->getCollection()->transform(function ($dataset) {
                if (is_string($dataset->tags)) {
                    $dataset->tags = json_decode($dataset->tags, true) ?? [];
                }
                if (!is_array($dataset->tags)) {
                    $dataset->tags = [];
                }
                $dataset->file_size_formatted = $this->formatFileSize($dataset->file_size);
                unset($dataset->file_path);
                return $dataset;
            });

            return $this->successResponse(
                [
                    'datasets' => $datasets->items(),
                    'pagination' => $this->getPaginationMeta($datasets),
                    'topic' => $topic,
                ],
                "Datasets for topic '{$topic}' retrieved successfully",
            );
        } catch (\Exception $e) {
            Log::error('Dataset getByTopic API error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve datasets by topic', 500);
        }
    }

    // Helper Methods (same as backend controller)

    /**
     * Apply common filters method (from backend controller)
     */
    private function applyCommonFilters($query, Request $request): void
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

        if ($request->has('file_type') && $request->file_type) {
            $query->where('file_type', $request->file_type);
        }

        if ($request->has('organization') && $request->organization) {
            $query->where('organization', 'like', "%{$request->organization}%");
        }

        if ($request->has('year') && $request->year) {
            $query->whereYear('created_at', $request->year);
        }
    }

    /**
     * Apply filters to dataset data (from backend controller)
     */
    private function applyDataFilters(array $data, Request $request): array
    {
        $filteredData = $data;

        $filters = $request->get('filter', []);
        if (!empty($filters)) {
            foreach ($filters as $column => $value) {
                if (!empty($value)) {
                    $filteredData = array_filter($filteredData, function ($row) use ($column, $value) {
                        return isset($row[$column]) && trim($row[$column]) == trim($value);
                    });
                }
            }
        }

        $searchTerm = $request->get('search');
        if (!empty($searchTerm)) {
            $filteredData = array_filter($filteredData, function ($row) use ($searchTerm) {
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

    /**
     * Format file size
     */
    private function formatFileSize($bytes): string
    {
        if (!$bytes) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Get pagination metadata
     */
    private function getPaginationMeta($paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'has_more_pages' => $paginator->hasMorePages(),
        ];
    }

    /**
     * Success response helper
     */
    private function successResponse($data = null, string $message = 'Success', int $statusCode = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
            'timestamp' => now()->toISOString(),
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Error response helper
     */
    private function errorResponse(string $message = 'Error', int $statusCode = 400, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
            'timestamp' => now()->toISOString(),
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Get analytics overview
     */
    public function getAnalyticsOverview(): JsonResponse
    {
        try {
            $analytics = Cache::remember('dataset_analytics', 1800, function () {
                return [
                    'total_datasets' => Dataset::where('approval_status', 'approved')->count(),
                    'total_downloads' => Dataset::where('approval_status', 'approved')->sum('download_count') ?? 0,
                    'total_views' => Dataset::where('approval_status', 'approved')->sum('view_count') ?? 0,
                    'datasets_this_month' => Dataset::where('approval_status', 'approved')->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
                    'popular_topics' => Dataset::where('approval_status', 'approved')->select('topic')->selectRaw('COUNT(*) as count')->whereNotNull('topic')->groupBy('topic')->orderBy('count', 'desc')->limit(5)->get(),
                    'popular_formats' => Dataset::where('approval_status', 'approved')->select('file_type')->selectRaw('COUNT(*) as count')->whereNotNull('file_type')->groupBy('file_type')->orderBy('count', 'desc')->get(),
                ];
            });

            return $this->successResponse($analytics, 'Analytics overview retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Dataset getAnalyticsOverview API error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve analytics', 500);
        }
    }

    // Additional methods to match backend functionality

    public function getFormats(): JsonResponse
    {
        try {
            $formats = Dataset::where('approval_status', 'approved')->distinct()->pluck('file_type')->filter()->sort()->values();

            return $this->successResponse($formats, 'Data formats retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve formats', 500);
        }
    }

    public function getAvailableYears(): JsonResponse
    {
        try {
            $years = Dataset::where('approval_status', 'approved')->selectRaw('YEAR(created_at) as year')->distinct()->orderBy('year', 'desc')->pluck('year')->values();

            return $this->successResponse($years, 'Available years retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve years', 500);
        }
    }

    public function getFeatured(Request $request): JsonResponse
    {
        $limit = min($request->get('limit', 10), 50);

        try {
            $datasets = Dataset::where('approval_status', 'approved')
                ->where('is_featured', true)
                ->with(['user'])
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($dataset) {
                    if (is_string($dataset->tags)) {
                        $dataset->tags = json_decode($dataset->tags, true) ?? [];
                    }
                    if (!is_array($dataset->tags)) {
                        $dataset->tags = [];
                    }
                    $dataset->file_size_formatted = $this->formatFileSize($dataset->file_size);
                    unset($dataset->file_path);
                    return $dataset;
                });

            return $this->successResponse($datasets, 'Featured datasets retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve featured datasets', 500);
        }
    }

    public function getPublicStats(): JsonResponse
    {
        try {
            $stats = [
                'total_dataset' => Dataset::where('approval_status', 'approved')->where('classification', 'publik')->count(),
                'total_mapset' => Mapset::where('is_visible', 'true')->count(),
                'total_visualisasi' => Visualisasi::where('is_public', 'true')->count(),
                'total_infografis' => Infografis::where('is_public', 'true')->count(),
            ];

            return $this->successResponse($stats, 'Statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve statistics', 500);
        }
    }
}
