<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class DatasetController extends Controller
{
    /**
     * Default pagination limit
     */
    protected int $defaultLimit = 15;
    protected int $maxLimit = 100;

    /**
     * Get list of datasets with pagination and filters
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Dataset::where('approval_status', 'approved')
                ->with(['user']);

            // Apply filters
            $this->applyCommonFilters($query, $request);

            // Apply sorting
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

            // Pagination
            $perPage = min($request->get('per_page', $this->defaultLimit), $this->maxLimit);
            $datasets = $query->paginate($perPage);

            // Process datasets like in backend controller
            $datasets->getCollection()->transform(function ($dataset) {
                // Process tags like in backend controller
                if (is_string($dataset->tags)) {
                    $dataset->tags = json_decode($dataset->tags, true) ?? [];
                }
                if (!is_array($dataset->tags)) {
                    $dataset->tags = [];
                }

                // Add formatted file size
                $dataset->file_size_formatted = $this->formatFileSize($dataset->file_size);
                
                // Remove sensitive file path for API
                unset($dataset->file_path);
                
                return $dataset;
            });

            return $this->successResponse([
                'datasets' => $datasets->items(),
                'pagination' => $this->getPaginationMeta($datasets),
                'filters' => $this->getAppliedFilters($request),
                'stats' => $this->getPublicStats()
            ], 'Datasets retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Dataset index API error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve datasets', 500);
        }
    }

    /**
     * Search datasets
     */
    public function search(Request $request): JsonResponse
    {
        $searchQuery = $request->get('q', '');
        $perPage = min($request->get('per_page', $this->defaultLimit), $this->maxLimit);

        if (empty($searchQuery)) {
            return $this->errorResponse('Search query is required', 400);
        }

        try {
            $query = Dataset::where('approval_status', 'approved')
                ->with(['user']);

            // Apply search like in backend controller
            $query->where(function ($q) use ($searchQuery) {
                $q->where('title', 'like', "%{$searchQuery}%")
                  ->orWhere('description', 'like', "%{$searchQuery}%")
                  ->orWhere('organization', 'like', "%{$searchQuery}%")
                  ->orWhere('filename', 'like', "%{$searchQuery}%")
                  ->orWhere('original_filename', 'like', "%{$searchQuery}%")
                  ->orWhere('sector', 'like', "%{$searchQuery}%")
                  ->orWhere('data_source', 'like', "%{$searchQuery}%")
                  ->orWhereJsonContains('tags', $searchQuery)
                  ->orWhere('tags', 'like', "%{$searchQuery}%");
            });

            $datasets = $query->orderBy('view_count', 'desc')
                             ->paginate($perPage);

            // Process results like in backend
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

            return $this->successResponse([
                'datasets' => $datasets->items(),
                'pagination' => $this->getPaginationMeta($datasets),
                'search_query' => $searchQuery,
                'total_found' => $datasets->total(),
            ], 'Search completed successfully');

        } catch (\Exception $e) {
            Log::error('Dataset search API error: ' . $e->getMessage());
            return $this->errorResponse('Search failed', 500);
        }
    }

    /**
     * Get single dataset details
     */
    public function show(string $slug): JsonResponse
    {
        try {
            $dataset = Dataset::where('slug', $slug)
                ->where('approval_status', 'approved')
                ->with(['user'])
                ->firstOrFail();

            // Increment view count like in backend
            if (Schema::hasColumn('datasets', 'view_count')) {
                $dataset->increment('view_count');
            }

            // Process tags like in backend
            if (is_string($dataset->tags)) {
                $dataset->tags = json_decode($dataset->tags, true) ?? [];
            }
            if (!is_array($dataset->tags)) {
                $dataset->tags = [];
            }

            $dataset->file_size_formatted = $this->formatFileSize($dataset->file_size);
            
            // Remove sensitive file path
            unset($dataset->file_path);

            return $this->successResponse([
                'dataset' => $dataset,
                'statistics' => [
                    'views' => $dataset->view_count ?? 0,
                    'downloads' => $dataset->download_count ?? 0,
                    'total_rows' => $dataset->total_rows ?? 0,
                    'total_columns' => $dataset->total_columns ?? count($dataset->headers ?? []),
                ],
                'download_url' => route('api.v1.datasets.download', $slug)
            ], 'Dataset retrieved successfully');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse('Dataset not found', 404);
        } catch (\Exception $e) {
            Log::error('Dataset show API error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve dataset', 500);
        }
    }

    /**
     * Get dataset data/content with pagination (like backend show method)
     */
    public function getData(string $slug, Request $request): JsonResponse
    {
        try {
            $dataset = Dataset::where('slug', $slug)
                ->where('approval_status', 'approved')
                ->firstOrFail();

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
                ]
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
     * Download dataset file (like backend download method)
     */
    public function download(string $slug)
    {
        try {
            $dataset = Dataset::where('slug', $slug)
                ->where('approval_status', 'approved')
                ->firstOrFail();

            if (!$dataset->file_path || !Storage::disk('public')->exists($dataset->file_path)) {
                return $this->errorResponse('File not found', 404);
            }

            // Increment download count like in backend
            if (Schema::hasColumn('datasets', 'download_count')) {
                $dataset->increment('download_count');
            }

            $originalFilename = $dataset->original_filename ?? $dataset->filename;

            return Storage::disk('public')->download(
                $dataset->file_path,
                $originalFilename
            );

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse('Dataset not found', 404);
        } catch (\Exception $e) {
            Log::error('Dataset download API error: ' . $e->getMessage());
            return $this->errorResponse('Download failed', 500);
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
                ->map(function($dataset) {
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
                ->map(function($dataset) {
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
            $topics = Cache::remember('dataset_topics', 3600, function() {
                return Dataset::where('approval_status', 'approved')
                    ->distinct()
                    ->pluck('topic')
                    ->filter()
                    ->sort()
                    ->values();
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

            return $this->successResponse([
                'datasets' => $datasets->items(),
                'pagination' => $this->getPaginationMeta($datasets),
                'topic' => $topic,
            ], "Datasets for topic '{$topic}' retrieved successfully");

        } catch (\Exception $e) {
            Log::error('Dataset getByTopic API error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve datasets by topic', 500);
        }
    }

    /**
     * Get public statistics (similar to getStatsBasedOnRole for public)
     */
    private function getPublicStats(): array
    {
        return Cache::remember('public_dataset_stats', 1800, function() {
            $statsQuery = Dataset::where('approval_status', 'approved');
            
            return [
                'total_datasets' => $statsQuery->count(),
                'total_views' => $statsQuery->sum('view_count') ?? 0,
                'total_downloads' => $statsQuery->sum('download_count') ?? 0,
                'datasets_this_month' => Dataset::where('approval_status', 'approved')
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
            ];
        });
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

    /**
     * Format file size
     */
    private function formatFileSize($bytes): string
    {
        if (!$bytes) return '0 B';
        
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
     * Get applied filters
     */
    private function getAppliedFilters(Request $request): array
    {
        return [
            'search' => $request->search,
            'topic' => $request->topic,
            'classification' => $request->classification,
            'status' => $request->status,
            'file_type' => $request->file_type,
            'organization' => $request->organization,
            'year' => $request->year,
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
            $analytics = Cache::remember('dataset_analytics', 1800, function() {
                return [
                    'total_datasets' => Dataset::where('approval_status', 'approved')->count(),
                    'total_downloads' => Dataset::where('approval_status', 'approved')->sum('download_count') ?? 0,
                    'total_views' => Dataset::where('approval_status', 'approved')->sum('view_count') ?? 0,
                    'datasets_this_month' => Dataset::where('approval_status', 'approved')
                        ->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year)
                        ->count(),
                    'popular_topics' => Dataset::where('approval_status', 'approved')
                        ->select('topic')
                        ->selectRaw('COUNT(*) as count')
                        ->whereNotNull('topic')
                        ->groupBy('topic')
                        ->orderBy('count', 'desc')
                        ->limit(5)
                        ->get(),
                    'popular_formats' => Dataset::where('approval_status', 'approved')
                        ->select('file_type')
                        ->selectRaw('COUNT(*) as count')
                        ->whereNotNull('file_type')
                        ->groupBy('file_type')
                        ->orderBy('count', 'desc')
                        ->get(),
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
            $formats = Dataset::where('approval_status', 'approved')
                ->distinct()
                ->pluck('file_type')
                ->filter()
                ->sort()
                ->values();

            return $this->successResponse($formats, 'Data formats retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve formats', 500);
        }
    }

    public function getAvailableYears(): JsonResponse
    {
        try {
            $years = Dataset::where('approval_status', 'approved')
                ->selectRaw('YEAR(created_at) as year')
                ->distinct()
                ->orderBy('year', 'desc')
                ->pluck('year')
                ->values();

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
                ->map(function($dataset) {
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
}