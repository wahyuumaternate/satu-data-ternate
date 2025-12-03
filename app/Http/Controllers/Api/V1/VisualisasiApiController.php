<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Visualisasi;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class VisualisasiApiController extends Controller
{
    /**
     * Default pagination limit
     */
    protected int $defaultLimit = 100;
    protected int $maxLimit = 100;

    /**
     * Get list of visualisasi with pagination and filters
     */
    public function index(Request $request): JsonResponse
{
    try {
        $query = Visualisasi::where('is_active', true)
            ->where('is_public', true)
            ->with(['user.organization']); // Tambahkan relasi organization

        // Apply filters like in backend controller
        $this->applyFilters($query, $request);

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        $allowedSortColumns = ['created_at', 'nama', 'views', 'topic', 'tipe'];

        if (in_array($sortBy, $allowedSortColumns)) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->latest();
        }

        // Pagination
        $perPage = min($request->get('per_page', $this->defaultLimit), $this->maxLimit);
        $visualisasi = $query->paginate($perPage);

        // Process visualisasi data
        $visualisasi->getCollection()->transform(function ($item) {
            // Remove sensitive file paths
            unset($item->source_file);
            
            // Add computed fields
            $item->file_exists = $item->source_file ? $item->fileExists() : false;
            $item->has_data = !empty($item->getProcessedData()['labels']);
            
            // Add organization name
            $item->organization_name = $item->user && $item->user->organization 
                ? $item->user->organization->name 
                : null;
            
            return $item;
        });

        return $this->successResponse([
            'visualisasi' => $visualisasi->items(),
            'pagination' => $this->getPaginationMeta($visualisasi),
            'filters' => $this->getAppliedFilters($request),
            'stats' => $this->getPublicStats()
        ], 'Visualisasi retrieved successfully');

    } catch (\Exception $e) {
        Log::error('Visualisasi index API error: ' . $e->getMessage());
        return $this->errorResponse('Failed to retrieve visualisasi', 500);
    }
}

    /**
     * Search visualisasi
     */
    public function search(Request $request): JsonResponse
    {
        $searchQuery = $request->get('q', '');
        $perPage = min($request->get('per_page', $this->defaultLimit), $this->maxLimit);

        if (empty($searchQuery)) {
            return $this->errorResponse('Search query is required', 400);
        }

        try {
            $query = Visualisasi::where('is_active', true)
                ->where('is_public', true)
                ->with(['user']);

            // Apply search like in backend controller
            $query->where(function($q) use ($searchQuery) {
                $q->where('nama', 'like', "%{$searchQuery}%")
                  ->orWhere('deskripsi', 'like', "%{$searchQuery}%")
                  ->orWhere('topic', 'like', "%{$searchQuery}%")
                  ->orWhere('tipe', 'like', "%{$searchQuery}%");
            });

            $visualisasi = $query->orderBy('views', 'desc')
                                ->paginate($perPage);

            // Process results
            $visualisasi->getCollection()->transform(function ($item) {
                unset($item->source_file);
                $item->file_exists = $item->source_file ? $item->fileExists() : false;
                $item->has_data = !empty($item->getProcessedData()['labels']);
                return $item;
            });

            return $this->successResponse([
                'visualisasi' => $visualisasi->items(),
                'pagination' => $this->getPaginationMeta($visualisasi),
                'search_query' => $searchQuery,
                'total_found' => $visualisasi->total(),
            ], 'Search completed successfully');

        } catch (\Exception $e) {
            Log::error('Visualisasi search API error: ' . $e->getMessage());
            return $this->errorResponse('Search failed', 500);
        }
    }

    /**
     * Get single visualisasi details
     */
   public function show(Visualisasi $visualisasi): JsonResponse
{
    try {
        // Validasi status
        if (!$visualisasi->is_active || !$visualisasi->is_public) {
            return $this->errorResponse('Visualisasi not found or not accessible', 404);
        }

        $visualisasi->load(['user.organization:id,name']);

        // Tambah view count
        $visualisasi->increment('views');

        return $this->successResponse([
            'visualisasi' => $visualisasi,
        ], 'Visualisasi retrieved successfully');

    } catch (\Throwable $e) {
        Log::error('Visualisasi show API error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
        ]);
        return $this->errorResponse('Failed to retrieve visualisasi', 500);
    }
}

    /**
     * Get visualisasi data for charts
     */
    public function getData(Visualisasi $visualisasi): JsonResponse
    {
        try {
            // Check if visualisasi is public and active
            if (!$visualisasi->is_active || !$visualisasi->is_public) {
                return $this->errorResponse('Visualisasi not found or not accessible', 404);
            }

            $processedData = $visualisasi->getProcessedData();

            if (empty($processedData['labels']) || empty($processedData['values'])) {
                return $this->errorResponse('No data available for this visualization', 404);
            }

            return $this->successResponse([
                'chart_data' => $processedData,
                'data_config' => $visualisasi->data_config,
                'chart_type' => $visualisasi->tipe,
                'labels_count' => count($processedData['labels']),
                'values_count' => count($processedData['values']),
            ], 'Visualisasi data retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Visualisasi getData API error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve visualisasi data', 500);
        }
    }

    /**
     * Get visualisasi configuration
     */
    public function getConfig(Visualisasi $visualisasi): JsonResponse
    {
        try {
            // Check if visualisasi is public and active
            if (!$visualisasi->is_active || !$visualisasi->is_public) {
                return $this->errorResponse('Visualisasi not found or not accessible', 404);
            }

            $config = [
                'chart_type' => $visualisasi->tipe,
                'topic' => $visualisasi->topic,
                'data_source' => $visualisasi->data_source,
                'data_config' => $visualisasi->data_config,
                'chart_options' => $this->getChartOptions($visualisasi->tipe),
                'recommended_dimensions' => $this->getRecommendedDimensions($visualisasi->tipe),
            ];

            return $this->successResponse($config, 'Visualisasi configuration retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Visualisasi getConfig API error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve visualisasi configuration', 500);
        }
    }

    /**
     * Export chart data as CSV
     */
    public function exportCsv(Visualisasi $visualisasi)
    {
        try {
            // Check if visualisasi is public and active
            if (!$visualisasi->is_active || !$visualisasi->is_public) {
                return $this->errorResponse('Visualisasi not found or not accessible', 404);
            }

            $data = $visualisasi->getProcessedData();
            
            if (empty($data['labels']) || empty($data['values'])) {
                return $this->errorResponse('No data available for export', 404);
            }

            $filename = Str::slug($visualisasi->nama) . '_data.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function() use ($data, $visualisasi) {
                $file = fopen('php://output', 'w');
                
                // Write headers
                $xLabel = $visualisasi->data_config['x_label'] ?? 'Label';
                $yLabel = $visualisasi->data_config['y_label'] ?? 'Value';
                fputcsv($file, [$xLabel, $yLabel]);
                
                // Write data
                for ($i = 0; $i < count($data['labels']); $i++) {
                    fputcsv($file, [$data['labels'][$i], $data['values'][$i] ?? 0]);
                }
                
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Visualisasi exportCsv API error: ' . $e->getMessage());
            return $this->errorResponse('Failed to export CSV', 500);
        }
    }

    /**
     * Export chart data as JSON
     */
    public function exportJson(Visualisasi $visualisasi): JsonResponse
    {
        try {
            // Check if visualisasi is public and active
            if (!$visualisasi->is_active || !$visualisasi->is_public) {
                return $this->errorResponse('Visualisasi not found or not accessible', 404);
            }

            $data = $visualisasi->getProcessedData();
            
            if (empty($data['labels']) || empty($data['values'])) {
                return $this->errorResponse('No data available for export', 404);
            }

            $filename = Str::slug($visualisasi->nama) . '_data.json';
            $exportData = [
                'visualisasi' => [
                    'nama' => $visualisasi->nama,
                    'tipe' => $visualisasi->tipe,
                    'topic' => $visualisasi->topic,
                    'deskripsi' => $visualisasi->deskripsi,
                    'created_at' => $visualisasi->created_at->toISOString(),
                ],
                'config' => $visualisasi->data_config,
                'data' => $data,
                'exported_at' => now()->toISOString(),
            ];

            return response()->json($exportData)
                ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");

        } catch (\Exception $e) {
            Log::error('Visualisasi exportJson API error: ' . $e->getMessage());
            return $this->errorResponse('Failed to export JSON', 500);
        }
    }

    /**
     * Get popular visualisasi
     */
    public function getPopular(Request $request): JsonResponse
    {
        $limit = min($request->get('limit', 10), 50);
        
        try {
            $visualisasi = Visualisasi::where('is_active', true)
                ->where('is_public', true)
                ->with(['user'])
                ->orderBy('views', 'desc')
                ->limit($limit)
                ->get()
                ->map(function($item) {
                    unset($item->source_file);
                    $item->has_data = !empty($item->getProcessedData()['labels']);
                    return $item;
                });

            return $this->successResponse($visualisasi, 'Popular visualisasi retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Visualisasi getPopular API error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve popular visualisasi', 500);
        }
    }

    /**
     * Get recent visualisasi
     */
    public function getRecent(Request $request): JsonResponse
    {
        $limit = min($request->get('limit', 10), 50);
        
        try {
            $visualisasi = Visualisasi::where('is_active', true)
                ->where('is_public', true)
                ->with(['user'])
                ->latest()
                ->limit($limit)
                ->get()
                ->map(function($item) {
                    unset($item->source_file);
                    $item->has_data = !empty($item->getProcessedData()['labels']);
                    return $item;
                });

            return $this->successResponse($visualisasi, 'Recent visualisasi retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Visualisasi getRecent API error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve recent visualisasi', 500);
        }
    }

    /**
     * Get featured visualisasi
     */
    public function getFeatured(Request $request): JsonResponse
    {
        $limit = min($request->get('limit', 10), 50);
        
        try {
            $visualisasi = Visualisasi::where('is_active', true)
                ->where('is_public', true)
                ->where('is_featured', true)
                ->with(['user'])
                ->latest()
                ->limit($limit)
                ->get()
                ->map(function($item) {
                    unset($item->source_file);
                    $item->has_data = !empty($item->getProcessedData()['labels']);
                    return $item;
                });

            return $this->successResponse($visualisasi, 'Featured visualisasi retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Visualisasi getFeatured API error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve featured visualisasi', 500);
        }
    }

    /**
     * Get available chart types
     */
    public function getTypes(): JsonResponse
    {
        try {
            $types = Cache::remember('visualisasi_types', 3600, function() {
                return Visualisasi::getTipes();
            });

            return $this->successResponse($types, 'Visualization types retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Visualisasi getTypes API error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve visualization types', 500);
        }
    }

    /**
     * Get available topics
     */
    public function getTopics(): JsonResponse
    {
        try {
            $topics = Cache::remember('visualisasi_topics', 3600, function() {
                return Visualisasi::getTopics();
            });

            return $this->successResponse($topics, 'Visualization topics retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Visualisasi getTopics API error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve visualization topics', 500);
        }
    }

    /**
     * Get visualisasi by type
     */
    public function getByType(Request $request, string $type): JsonResponse
    {
        $perPage = min($request->get('per_page', $this->defaultLimit), $this->maxLimit);
        
        try {
            $visualisasi = Visualisasi::where('is_active', true)
                ->where('is_public', true)
                ->byTipe($type)
                ->with(['user'])
                ->latest()
                ->paginate($perPage);

            $visualisasi->getCollection()->transform(function ($item) {
                unset($item->source_file);
                $item->has_data = !empty($item->getProcessedData()['labels']);
                return $item;
            });

            return $this->successResponse([
                'visualisasi' => $visualisasi->items(),
                'pagination' => $this->getPaginationMeta($visualisasi),
                'type' => $type,
            ], "Visualisasi of type '{$type}' retrieved successfully");

        } catch (\Exception $e) {
            Log::error('Visualisasi getByType API error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve visualisasi by type', 500);
        }
    }

    /**
     * Get visualisasi by topic
     */
    public function getByTopic(Request $request, string $topic): JsonResponse
    {
        $perPage = min($request->get('per_page', $this->defaultLimit), $this->maxLimit);
        
        try {
            $visualisasi = Visualisasi::where('is_active', true)
                ->where('is_public', true)
                ->byTopic($topic)
                ->with(['user'])
                ->latest()
                ->paginate($perPage);

            $visualisasi->getCollection()->transform(function ($item) {
                unset($item->source_file);
                $item->has_data = !empty($item->getProcessedData()['labels']);
                return $item;
            });

            return $this->successResponse([
                'visualisasi' => $visualisasi->items(),
                'pagination' => $this->getPaginationMeta($visualisasi),
                'topic' => $topic,
            ], "Visualisasi for topic '{$topic}' retrieved successfully");

        } catch (\Exception $e) {
            Log::error('Visualisasi getByTopic API error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve visualisasi by topic', 500);
        }
    }

    /**
     * Get visualisasi by organization
     */
    public function getByOrganization(Request $request, string $organization): JsonResponse
    {
        $perPage = min($request->get('per_page', $this->defaultLimit), $this->maxLimit);
        
        try {
            $visualisasi = Visualisasi::where('is_active', true)
                ->where('is_public', true)
                ->whereHas('user', function($query) use ($organization) {
                    $query->where('organization', 'like', '%' . $organization . '%');
                })
                ->with(['user'])
                ->latest()
                ->paginate($perPage);

            $visualisasi->getCollection()->transform(function ($item) {
                unset($item->source_file);
                $item->has_data = !empty($item->getProcessedData()['labels']);
                return $item;
            });

            return $this->successResponse([
                'visualisasi' => $visualisasi->items(),
                'pagination' => $this->getPaginationMeta($visualisasi),
                'organization' => $organization,
            ], "Visualisasi for organization '{$organization}' retrieved successfully");

        } catch (\Exception $e) {
            Log::error('Visualisasi getByOrganization API error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve visualisasi by organization', 500);
        }
    }

    /**
     * Export visualisasi catalog
     */
    public function exportCatalog(Request $request): JsonResponse
    {
        try {
            $visualisasi = Visualisasi::where('is_active', true)
                ->where('is_public', true)
                ->with(['user'])
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'nama' => $item->nama,
                        'deskripsi' => $item->deskripsi,
                        'tipe' => $item->tipe,
                        'topic' => $item->topic,
                        'data_source' => $item->data_source,
                        'views' => $item->views,
                        'organization' => $item->user->organization ?? '',
                        'creator' => $item->user->name,
                        'created_at' => $item->created_at,
                        'updated_at' => $item->updated_at,
                        'has_data' => !empty($item->getProcessedData()['labels']),
                    ];
                });

            return $this->successResponse([
                'catalog' => $visualisasi,
                'total_visualizations' => $visualisasi->count(),
                'exported_at' => now()
            ], 'Visualisasi catalog exported successfully');

        } catch (\Exception $e) {
            Log::error('Visualisasi exportCatalog API error: ' . $e->getMessage());
            return $this->errorResponse('Failed to export catalog', 500);
        }
    }

    // Helper Methods

    /**
     * Apply filters (based on backend controller)
     */
    private function applyFilters($query, Request $request): void
    {
        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        // Topic filter
        if ($request->filled('topic')) {
            $query->byTopic($request->topic);
        }

        // Type filter
        if ($request->filled('tipe') || $request->filled('type')) {
            $type = $request->tipe ?? $request->type;
            $query->byTipe($type);
        }

        // Data source filter
        if ($request->filled('data_source')) {
            $query->where('data_source', $request->data_source);
        }
    }

    /**
     * Get public statistics
     */
    private function getPublicStats(): array
    {
        return Cache::remember('public_visualisasi_stats', 1800, function() {
            return [
                'total_visualizations' => Visualisasi::where('is_active', true)->where('is_public', true)->count(),
                'total_views' => Visualisasi::where('is_active', true)->where('is_public', true)->sum('views') ?? 0,
                'visualizations_this_month' => Visualisasi::where('is_active', true)
                    ->where('is_public', true)
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'by_type' => Visualisasi::where('is_active', true)
                    ->where('is_public', true)
                    ->select('tipe')
                    ->selectRaw('COUNT(*) as count')
                    ->groupBy('tipe')
                    ->get()
                    ->pluck('count', 'tipe'),
                'by_topic' => Visualisasi::where('is_active', true)
                    ->where('is_public', true)
                    ->select('topic')
                    ->selectRaw('COUNT(*) as count')
                    ->groupBy('topic')
                    ->get()
                    ->pluck('count', 'topic'),
            ];
        });
    }

    /**
     * Get chart options based on type
     */
    private function getChartOptions(string $chartType): array
    {
        $options = [
            'bar_chart' => [
                'responsive' => true,
                'scales' => ['x' => ['display' => true], 'y' => ['display' => true]],
                'plugins' => ['legend' => ['display' => true]]
            ],
            'line_chart' => [
                'responsive' => true,
                'scales' => ['x' => ['display' => true], 'y' => ['display' => true]],
                'elements' => ['line' => ['tension' => 0.1]]
            ],
            'pie_chart' => [
                'responsive' => true,
                'plugins' => ['legend' => ['position' => 'bottom']]
            ],
            'area_chart' => [
                'responsive' => true,
                'fill' => true,
                'scales' => ['x' => ['display' => true], 'y' => ['display' => true]]
            ],
        ];

        return $options[$chartType] ?? $options['bar_chart'];
    }

    /**
     * Get recommended dimensions for chart type
     */
    private function getRecommendedDimensions(string $chartType): array
    {
        $dimensions = [
            'bar_chart' => ['width' => 800, 'height' => 400],
            'line_chart' => ['width' => 800, 'height' => 400],
            'pie_chart' => ['width' => 500, 'height' => 500],
            'area_chart' => ['width' => 800, 'height' => 400],
            'scatter_plot' => ['width' => 600, 'height' => 600],
            'histogram' => ['width' => 700, 'height' => 400],
        ];

        return $dimensions[$chartType] ?? $dimensions['bar_chart'];
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
            'type' => $request->tipe ?? $request->type,
            'data_source' => $request->data_source,
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
}