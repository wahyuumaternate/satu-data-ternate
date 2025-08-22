<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Mapset;
use App\Models\MapsetFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class MapsetController extends Controller
{
    /**
     * Display a listing of public mapsets.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Mapset::with([
                        'user.organization:id,name'
                    ])
                ->where('is_active', true)
                ->where('is_visible', true);

            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('nama', 'like', "%{$searchTerm}%")
                      ->orWhere('deskripsi', 'like', "%{$searchTerm}%")
                      ->orWhere('topic', 'like', "%{$searchTerm}%");
                });
            }

            // Filter by topic
            if ($request->has('topic') && !empty($request->topic)) {
                $query->where('topic', $request->topic);
            }

            // Filter by category (alias for topic for consistency with datasets)
            if ($request->has('category') && !empty($request->category)) {
                $query->where('topic', $request->category);
            }

            // Sorting
            $sortBy = $request->get('sort', 'created_at');
            $sortOrder = $request->get('order', 'desc');
            
            switch ($sortBy) {
                case 'nama':
                case 'title':
                    $query->orderBy('nama', $sortOrder);
                    break;
                case 'views':
                case 'view_count':
                    $query->orderBy('views', $sortOrder);
                    break;
                case 'topic':
                case 'category':
                    $query->orderBy('topic', $sortOrder);
                    break;
                case 'updated_at':
                    $query->orderBy('updated_at', $sortOrder);
                    break;
                default:
                    $query->orderBy('created_at', $sortOrder);
            }

            // Pagination
            $perPage = min($request->get('per_page', 12), 50); // Max 50 items per page
            $mapsets = $query->paginate($perPage);

            // Transform data
            $transformedMapsets = $mapsets->map(function ($mapset) {
                return $this->transformMapsetForList($mapset);
            });

            // Get available topics
            $availableTopics = $this->getAvailableTopics();

            return response()->json([
                'success' => true,
                'message' => 'Mapsets retrieved successfully',
                'timestamp' => now()->toISOString(),
                'data' => [
                    'mapsets' => $transformedMapsets,
                    'pagination' => [
                        'current_page' => $mapsets->currentPage(),
                        'last_page' => $mapsets->lastPage(),
                        'per_page' => $mapsets->perPage(),
                        'total' => $mapsets->total(),
                        'from' => $mapsets->firstItem(),
                        'to' => $mapsets->lastItem(),
                        'has_more_pages' => $mapsets->hasMorePages(),
                        'path' => $mapsets->path(),
                        'first_page_url' => $mapsets->url(1),
                        'last_page_url' => $mapsets->url($mapsets->lastPage()),
                        'next_page_url' => $mapsets->nextPageUrl(),
                        'prev_page_url' => $mapsets->previousPageUrl(),
                    ],
                    'filters' => [
                        'available_topics' => $availableTopics,
                        'current_search' => $request->get('search'),
                        'current_topic' => $request->get('topic'),
                        'current_sort' => $sortBy,
                        'current_order' => $sortOrder,
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching mapsets: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve mapsets',
                'timestamp' => now()->toISOString(),
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Display the specified mapset.
     * 
     * @param string $identifier - UUID or slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($identifier)
    {
        try {
            // Find by UUID or slug
            $mapset = Mapset::with([
                        'user:id,name',
                        'user.organization:id,name'
                    ])
                ->where('is_active', true)
                ->where('is_visible', true)
                ->where(function($query) use ($identifier) {
                    $query->where('slug', $identifier);
                })
                ->first();

            if (!$mapset) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mapset not found or not publicly available',
                    'timestamp' => now()->toISOString(),
                ], 404);
            }

            // Increment views (with caching to prevent spam)
            $this->incrementViews($mapset);

            // Get GeoJSON data
            $geojson = $this->getMapsetGeoJSON($mapset->id);
            
            // Get bounds
            $bounds = $this->getMapsetBounds($mapset->id);

            // Get statistics
            $stats = $this->getMapsetStats($mapset->id);

            // Transform mapset data
            $transformedMapset = $this->transformMapsetForDetail($mapset, $geojson, $bounds, $stats);

            return response()->json([
                'success' => true,
                'message' => 'Mapset retrieved successfully',
                'timestamp' => now()->toISOString(),
                'data' => [
                    'mapset' => $transformedMapset
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching mapset: ' . $e->getMessage(), [
                'identifier' => $identifier,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve mapset',
                'timestamp' => now()->toISOString(),
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get GeoJSON data for a specific mapset.
     * 
     * @param string $identifier - UUID or slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function geojson($identifier)
    {
        try {
            // Find mapset
            $mapset = Mapset::where('is_active', true)
                ->where('is_visible', true)
                ->where(function($query) use ($identifier) {
                    $query->where('uuid', $identifier)
                          ->orWhere('slug', $identifier);
                })
                ->first();

            if (!$mapset) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mapset not found or not publicly available',
                    'timestamp' => now()->toISOString(),
                ], 404);
            }

            // Get GeoJSON with caching
            $geojson = Cache::remember(
                "mapset_geojson_{$mapset->id}", 
                now()->addHours(1), 
                function() use ($mapset) {
                    return $this->getMapsetGeoJSON($mapset->id);
                }
            );

            if (!$geojson || empty($geojson['features'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No geographic data available for this mapset',
                    'timestamp' => now()->toISOString(),
                ], 404);
            }

            // Add metadata
            $geojson['metadata'] = [
                'mapset_id' => $mapset->id,
                'mapset_uuid' => $mapset->uuid,
                'mapset_name' => $mapset->nama,
                'mapset_description' => $mapset->deskripsi,
                'topic' => $mapset->topic,
                'feature_count' => count($geojson['features']),
                'generated_at' => now()->toISOString(),
            ];

            return response()->json($geojson, 200, [
                'Content-Type' => 'application/geo+json'
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching mapset GeoJSON: ' . $e->getMessage(), [
                'identifier' => $identifier,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve GeoJSON data',
                'timestamp' => now()->toISOString(),
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get public statistics for mapsets.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats()
    {
        try {
            $stats = Cache::remember('mapsets_public_stats', now()->addMinutes(15), function() {
                return [
                    'total_mapsets' => Mapset::where('is_active', true)
                        ->where('is_visible', true)
                        ->count(),
                    'total_views' => Mapset::where('is_active', true)
                        ->where('is_visible', true)
                        ->sum('views'),
                    'total_features' => DB::table('mapset_features')
                        ->whereIn('mapset_id', function($query) {
                            $query->select('id')
                                ->from('mapsets')
                                ->where('is_active', true)
                                ->where('is_visible', true);
                        })
                        ->count(),
                    'topics_count' => Mapset::where('is_active', true)
                        ->where('is_visible', true)
                        ->groupBy('topic')
                        ->pluck('topic')
                        ->map(function($topic) {
                            return [
                                'topic' => $topic,
                                'count' => Mapset::where('is_active', true)
                                    ->where('is_visible', true)
                                    ->where('topic', $topic)
                                    ->count()
                            ];
                        })
                        ->values(),
                    'recent_mapsets' => Mapset::where('is_active', true)
                        ->where('is_visible', true)
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get(['uuid', 'nama', 'topic', 'created_at'])
                        ->map(function($mapset) {
                            return [
                                'uuid' => $mapset->uuid,
                                'title' => $mapset->nama,
                                'topic' => $mapset->topic,
                                'created_at' => $mapset->created_at->toISOString(),
                            ];
                        }),
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Mapset statistics retrieved successfully',
                'timestamp' => now()->toISOString(),
                'data' => $stats
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching mapset statistics: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve mapset statistics',
                'timestamp' => now()->toISOString(),
                'error' => app()->environment('local') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    // === HELPER METHODS ===

    /**
     * Get available topics from published mapsets.
     */
    private function getAvailableTopics()
    {
        return Cache::remember('mapsets_available_topics', now()->addHours(1), function() {
            return Mapset::where('is_active', true)
                ->where('is_visible', true)
                ->distinct()
                ->pluck('topic')
                ->filter()
                ->sort()
                ->values()
                ->map(function($topic) {
                    return [
                        'id' => strtolower(str_replace([' ', '&'], ['_', '_'], $topic)),
                        'name' => $topic,
                        'count' => Mapset::where('is_active', true)
                            ->where('is_visible', true)
                            ->where('topic', $topic)
                            ->count()
                    ];
                });
        });
    }

    /**
     * Increment view count with caching to prevent spam.
     */
    private function incrementViews($mapset)
    {
        $cacheKey = "mapset_view_{$mapset->id}_" . request()->ip() . "_" . now()->format('Y-m-d-H');
        
        if (!Cache::has($cacheKey)) {
            $mapset->increment('views');
            Cache::put($cacheKey, true, now()->addHour());
        }
    }

    /**
     * Transform mapset data for list view.
     */
    private function transformMapsetForList($mapset)
    {
        return [
            'id' => $mapset->id,
            'uuid' => $mapset->uuid,
            'slug' => $mapset->slug,
            'title' => $mapset->nama,
            'description' => $mapset->deskripsi,
            'topic' => $mapset->topic,
            'category_id' => strtolower(str_replace([' ', '&'], ['_', '_'], $mapset->topic)), // For frontend compatibility
            'thumbnail' => $mapset->gambar ? asset('storage/mapsets/' . $mapset->gambar) : null,
            'organization_name' => $mapset->user->organization->name ?? $mapset->user->name ?? 'Unknown Organization',
            'organization_id' => $mapset->user->organization->id ?? null,
            'author' => $mapset->user->name ?? 'Unknown Author',
            'view_count' => $mapset->views ?? 0,
            'download_count' => 0, // Mapsets don't track downloads like datasets
            'is_visible' => $mapset->is_visible,
            'is_active' => $mapset->is_active,
            'created_at' => $mapset->created_at->toISOString(),
            'updated_at' => $mapset->updated_at->toISOString(),
        ];
    }

    /**
     * Transform mapset data for detail view.
     */
    private function transformMapsetForDetail($mapset, $geojson, $bounds, $stats)
    {
        return [
            'id' => $mapset->id,
            'uuid' => $mapset->uuid,
            'slug' => $mapset->slug,
            'title' => $mapset->nama,
            'description' => $mapset->deskripsi,
            'topic' => $mapset->topic,
            'category_id' => strtolower(str_replace([' ', '&'], ['_', '_'], $mapset->topic)),
            'thumbnail' => $mapset->gambar ? asset('storage/mapsets/' . $mapset->gambar) : null,
            'organization_name' => $mapset->user->organization->name ?? $mapset->user->name ?? 'Unknown Organization',
            'organization_id' => $mapset->user->organization->id ?? null,
            'author' => $mapset->user->name ?? 'Unknown Author',
            'view_count' => $mapset->views ?? 0,
            'download_count' => 0,
            'is_visible' => $mapset->is_visible,
            'is_active' => $mapset->is_active,
            'created_at' => $mapset->created_at->toISOString(),
            'updated_at' => $mapset->updated_at->toISOString(),
            
            // Geographic data
            'geojson' => $geojson,
            'bounds' => $bounds,
            'statistics' => $stats,
            
            // Additional metadata
            'meta' => [
                'feature_count' => $stats['feature_count'] ?? 0,
                'geometry_types' => $stats['geometry_types'] ?? [],
                'has_geographic_data' => !empty($geojson['features']),
                'coordinate_system' => 'WGS84 (EPSG:4326)',
            ]
        ];
    }

    /**
     * Get GeoJSON for mapset features.
     */
    private function getMapsetGeoJSON($mapsetId)
    {
        try {
            $features = DB::select("
                SELECT 
                    id,
                    ST_AsGeoJSON(geom) as geojson,
                    attributes
                FROM mapset_features 
                WHERE mapset_id = ? AND geom IS NOT NULL
            ", [$mapsetId]);

            if (empty($features)) {
                return [
                    'type' => 'FeatureCollection',
                    'features' => []
                ];
            }

            $geojsonFeatures = [];
            foreach ($features as $feature) {
                $geometry = json_decode($feature->geojson, true);
                $attributes = $feature->attributes ? json_decode($feature->attributes, true) : [];

                $geojsonFeatures[] = [
                    'type' => 'Feature',
                    'properties' => array_merge($attributes, ['feature_id' => $feature->id]),
                    'geometry' => $geometry
                ];
            }

            return [
                'type' => 'FeatureCollection',
                'features' => $geojsonFeatures
            ];

        } catch (\Exception $e) {
            Log::error('Error getting GeoJSON for mapset ' . $mapsetId . ': ' . $e->getMessage());
            return [
                'type' => 'FeatureCollection',
                'features' => []
            ];
        }
    }

    /**
     * Get bounds for mapset features.
     */
    private function getMapsetBounds($mapsetId)
    {
        try {
            $result = DB::select("
                SELECT 
                    ST_XMin(ST_Envelope(ST_Collect(geom))) as min_lng,
                    ST_YMin(ST_Envelope(ST_Collect(geom))) as min_lat,
                    ST_XMax(ST_Envelope(ST_Collect(geom))) as max_lng,
                    ST_YMax(ST_Envelope(ST_Collect(geom))) as max_lat
                FROM mapset_features 
                WHERE mapset_id = ? AND geom IS NOT NULL
            ", [$mapsetId]);

            if ($result && $result[0] && $result[0]->min_lng !== null) {
                return [
                    [$result[0]->min_lat, $result[0]->min_lng],
                    [$result[0]->max_lat, $result[0]->max_lng]
                ];
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error getting bounds for mapset ' . $mapsetId . ': ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get statistics for mapset features.
     */
    private function getMapsetStats($mapsetId)
    {
        try {
            // Get feature count and geometry types in one query
            $stats = DB::select("
                SELECT 
                    COUNT(*) as feature_count,
                    COUNT(DISTINCT ST_GeometryType(geom)) as geometry_type_count
                FROM mapset_features 
                WHERE mapset_id = ? AND geom IS NOT NULL
            ", [$mapsetId]);

            // Get distinct geometry types separately (PostgreSQL compatible)
            $geometryTypes = DB::select("
                SELECT DISTINCT ST_GeometryType(geom) as geom_type
                FROM mapset_features 
                WHERE mapset_id = ? AND geom IS NOT NULL
            ", [$mapsetId]);

            if ($stats && $stats[0]) {
                $types = array_map(function($row) {
                    return str_replace('ST_', '', $row->geom_type);
                }, $geometryTypes);

                return [
                    'feature_count' => (int) $stats[0]->feature_count,
                    'geometry_type_count' => (int) $stats[0]->geometry_type_count,
                    'geometry_types' => $types
                ];
            }

            return [
                'feature_count' => 0,
                'geometry_type_count' => 0,
                'geometry_types' => []
            ];

        } catch (\Exception $e) {
            Log::error('Error getting stats for mapset ' . $mapsetId . ': ' . $e->getMessage());
            return [
                'feature_count' => 0,
                'geometry_type_count' => 0,
                'geometry_types' => []
            ];
        }
    }
}