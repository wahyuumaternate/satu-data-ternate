<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Dataset;
use App\Models\Infografis;
use App\Models\Mapset;
use App\Models\Visualisasi;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OrganizationApiController extends Controller
{
    /**
     * Default pagination limit
     */
    protected int $defaultLimit = 15;
    protected int $maxLimit = 100;

    /**
     * Get list of organizations with pagination and filters
     */
    public function index(Request $request): JsonResponse
{
    try {
        $query = Organization::query();

        // Apply search filter (menggunakan scope yang sama dengan backend)
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Apply sorting (menggunakan logika yang sama dengan backend)
        $sort = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc');
        
        switch ($sort) {
            case 'name':
                $query->orderBy('name', $direction);
                break;
            case 'code':
                $query->orderBy('code', $direction);
                break;
            case 'created_at':
                $query->orderBy('created_at', $direction);
                break;
            default:
                $query->orderBy('name', 'asc');
                break;
        }

        // Add counts untuk content yang dibuat oleh users dari organization ini
        $query->withCount(['users'])
              ->addSelect([
                  'organizations.*', // Pastikan semua field organization tetap ada
                  
                  // Count datasets yang approved dan publik
                  'total_datasets' => DB::table('datasets')
                      ->join('users', 'datasets.user_id', '=', 'users.id')
                      ->whereColumn('users.organization_id', 'organizations.id')
                      ->where('datasets.approval_status', 'approved')
                      ->where('datasets.classification', 'publik')
                      ->selectRaw('count(*)'),
                  
                  // Count infografis yang public
                  'total_infografis' => DB::table('infografis')
                      ->join('users', 'infografis.user_id', '=', 'users.id')
                      ->whereColumn('users.organization_id', 'organizations.id')
                      ->where('infografis.is_public', true)
                      ->selectRaw('count(*)'),
                  
                  // Count mapsets
                  'total_mapsets' => DB::table('mapsets')
                      ->join('users', 'mapsets.user_id', '=', 'users.id')
                      ->whereColumn('users.organization_id', 'organizations.id')
                      ->selectRaw('count(*)'),
                  
                  // Count visualisasi yang active dan public
                  'total_visualisasi' => DB::table('visualisasi')
                      ->join('users', 'visualisasi.user_id', '=', 'users.id')
                      ->whereColumn('users.organization_id', 'organizations.id')
                      ->where('visualisasi.is_active', true)
                      ->where('visualisasi.is_public', true)
                      ->selectRaw('count(*)')
              
              ]);

        // Pagination
        $perPage = max(1, min((int)$request->get('per_page', 15), 100));
        $organizations = $query->paginate($perPage);

        // Transform data
        $organizations->getCollection()->transform(function ($organization) {
            // Menggunakan accessor yang sudah ada di model
            $organization->logo_url = $organization->logo_url;
            $organization->formatted_website = $organization->formatted_website;
            
            // Count data dari addSelect query
            $organization->users_count = $organization->users_count ?? 0;
            $organization->total_datasets = $organization->total_datasets ?? 0;
            $organization->total_infografis = $organization->total_infografis ?? 0;
            $organization->total_mapsets = $organization->total_mapsets ?? 0;
            $organization->total_visualisasi = $organization->total_visualisasi ?? 0;
            
            // Debug: cek apakah field ada
            Log::info('Organization counts', [
                'id' => $organization->id,
                'name' => $organization->name,
                'datasets' => $organization->total_datasets,
                'infografis' => $organization->total_infografis,
                'mapsets' => $organization->total_mapsets,
                'visualisasi' => $organization->total_visualisasi,
                'attributes' => $organization->getAttributes()
            ]);
            
            // Total content
            $organization->total_content = 
                $organization->total_datasets + 
                $organization->total_infografis + 
                $organization->total_mapsets + 
                $organization->total_visualisasi;
            
            return $organization;
        });

        // Response data
        $responseData = [
            'organizations' => $organizations->items(),
            'pagination' => [
                'total' => $organizations->total(),
                'per_page' => $organizations->perPage(),
                'current_page' => $organizations->currentPage(),
                'last_page' => $organizations->lastPage(),
                'from' => $organizations->firstItem(),
                'to' => $organizations->lastItem(),
                'has_more_pages' => $organizations->hasMorePages(),
            ],
            'filters' => [
                'search' => $request->get('search'),
                'sort' => $sort,
                'direction' => $direction,
            ]
        ];

        // Add stats (menggunakan method yang sama dengan backend)
        try {
            $responseData['stats'] = [
                'total' => Organization::count(),
                'this_month' => Organization::whereMonth('created_at', now()->month)->count(),
                'with_website' => Organization::whereNotNull('website')->count(),
                'with_logo' => Organization::whereNotNull('logo')->count(),
            ];
        } catch (\Exception $e) {
            Log::warning('Failed to get organization stats: ' . $e->getMessage());
        }

        // Return response menggunakan helper method jika ada
        if (method_exists($this, 'successResponse')) {
            return $this->successResponse($responseData, 'Organizations retrieved successfully');
        }

        // Fallback response
        return response()->json([
            'success' => true,
            'data' => $responseData,
            'message' => 'Organizations retrieved successfully'
        ]);

    } catch (\Illuminate\Database\QueryException $e) {
        Log::error('Database error in Organization API index', [
            'message' => $e->getMessage(),
            'sql' => $e->getSql(),
            'bindings' => $e->getBindings(),
            'request' => $request->all()
        ]);
        
        return $this->handleError('Database error occurred', 500);
        
    } catch (\Exception $e) {
        Log::error('Organization API index error', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'request' => $request->all()
        ]);
        
        return $this->handleError('Failed to retrieve organizations', 500);
    }
}

private function handleError($message, $code = 500)
{
    // Gunakan method dari base controller jika ada
    if (method_exists($this, 'errorResponse')) {
        return $this->errorResponse($message, $code);
    }
    
    // Fallback response
    return response()->json([
        'success' => false,
        'message' => $message,
        'timestamp' => now()->toISOString()
    ], $code);
}

    /**
     * Search organizations
     */
    public function search(Request $request): JsonResponse
    {
        $searchQuery = $request->get('query', '');
        $perPage = min($request->get('per_page', $this->defaultLimit), $this->maxLimit);
        if (empty($searchQuery)) {
            return $this->errorResponse('Search query is required', 400);
        }

        try {
            $query = Organization::query();

            // Apply search like in backend controller
            $query->search($searchQuery);

            // Add content counts
            $query->withCount([
                'datasets as total_datasets' => function($q) {
                    $q->where('approval_status', 'approved');
                },
                'infografis as total_infografis' => function($q) {
                    $q->where('is_public', true);
                },
                'mapsets as total_mapsets',
                'visualisasi as total_visualisasi' => function($q) {
                    $q->where('is_active', true)->where('is_public', true);
                }
            ]);

            $organizations = $query->orderBy('name', 'asc')
                                  ->paginate($perPage);

            // Process results
            $organizations->getCollection()->transform(function ($organization) {
                $organization->total_content = 
                    $organization->total_datasets + 
                    $organization->total_infografis + 
                    $organization->total_mapsets + 
                    $organization->total_visualisasi;
                
                $organization->logo_url = $organization->logo ? 
                    asset('storage/' . $organization->logo) : null;
                
                return $organization;
            });

            return $this->successResponse([
                'organizations' => $organizations->items(),
                'pagination' => $this->getPaginationMeta($organizations),
                'search_query' => $searchQuery,
                'total_found' => $organizations->total(),
            ], 'Search completed successfully');

        } catch (\Exception $e) {
            Log::error('Organization search API error: ' . $e->getMessage());
            return $this->errorResponse('Search failed', 500);
        }
    }


/**
 * Get single organization details with content data
 */
public function show(Organization $organization): JsonResponse
{
    try {
        // Get datasets yang dibuat oleh users dari organization ini
        $datasets = Dataset::whereHas('user', function($q) use ($organization) {
                $q->where('organization_id', $organization->id);
            })
            ->where('approval_status', 'approved')
            ->where('classification', 'publik')
            ->with(['user:id,name'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($dataset) {
                // Process tags
                if (is_string($dataset->tags)) {
                    $dataset->tags = json_decode($dataset->tags, true) ?? [];
                }
                if (!is_array($dataset->tags)) {
                    $dataset->tags = [];
                }
                
                // Remove sensitive paths
                unset($dataset->file_path);
                
                return $dataset;
            });

        // Get infografis yang dibuat oleh users dari organization ini
        $infografis = Infografis::whereHas('user', function($q) use ($organization) {
                $q->where('organization_id', $organization->id);
            })
            ->where('is_public', true)
            ->with(['user:id,name'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get mapsets yang dibuat oleh users dari organization ini
        $mapsets = Mapset::whereHas('user', function($q) use ($organization) {
                $q->where('organization_id', $organization->id);
            })
            ->with(['user:id,name'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get visualisasi yang dibuat oleh users dari organization ini
        $visualisasi = Visualisasi::whereHas('user', function($q) use ($organization) {
                $q->where('organization_id', $organization->id);
            })
            ->where('is_active', true)
            ->where('is_public', true)
            ->with(['user:id,name'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Add computed fields using model accessors
        $organization->total_datasets = $datasets->count();
        $organization->total_infografis = $infografis->count();
        $organization->total_mapsets = $mapsets->count();
        $organization->total_visualisasi = $visualisasi->count();
        $organization->total_content = 
            $organization->total_datasets + 
            $organization->total_infografis + 
            $organization->total_mapsets + 
            $organization->total_visualisasi;

        $organization->logo_url = $organization->logo_url; // Menggunakan accessor model
        $organization->formatted_website = $organization->formatted_website; // Menggunakan accessor model

        $responseData = [
            'organization' => $organization,
            'content' => [
                'datasets' => $datasets,
                'infografis' => $infografis,
                'mapsets' => $mapsets,
                'visualisasi' => $visualisasi,
            ],
            'statistics' => [
                'content_breakdown' => [
                    'datasets' => $organization->total_datasets,
                    'infografis' => $organization->total_infografis,
                    'mapsets' => $organization->total_mapsets,
                    'visualisasi' => $organization->total_visualisasi,
                    'total' => $organization->total_content
                ],
                'created_at' => $organization->created_at->format('Y-m-d'),
                'updated_at' => $organization->updated_at->format('Y-m-d'),
                'has_website' => !empty($organization->website),
                'has_logo' => !empty($organization->logo),
            ]
        ];

        return $this->successResponse($responseData, 'Organization retrieved successfully');

    } catch (\Illuminate\Database\QueryException $e) {
        Log::error('Database error in Organization show', [
            'organization_id' => $organization->id ?? 'unknown',
            'message' => $e->getMessage(),
            'sql' => $e->getSql(),
            'bindings' => $e->getBindings()
        ]);
        
        return $this->errorResponse('Database error occurred', 500);
        
    } catch (\Exception $e) {
        Log::error('Organization show API error', [
            'organization_id' => $organization->id ?? 'unknown',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return $this->errorResponse('Failed to retrieve organization', 500);
    }
}


/**
 * Get organization statistics
 */
private function getOrganizationStats(Organization $organization)
{
    try {
        return [
            'users_count' => $organization->users_count,
            'content_breakdown' => [
                'datasets' => $organization->total_datasets,
                'infografis' => $organization->total_infografis,
                'mapsets' => $organization->total_mapsets,
                'visualisasi' => $organization->total_visualisasi,
                'total' => $organization->total_content
            ],
            'created_at' => $organization->created_at->format('Y-m-d'),
            'updated_at' => $organization->updated_at->format('Y-m-d'),
            'has_website' => !empty($organization->website),
            'has_logo' => !empty($organization->logo),
        ];
    } catch (\Exception $e) {
        Log::warning('Error generating organization stats: ' . $e->getMessage());
        
        return [
            'users_count' => 0,
            'content_breakdown' => [
                'datasets' => 0,
                'infografis' => 0,
                'mapsets' => 0,
                'visualisasi' => 0,
                'total' => 0
            ],
            'created_at' => null,
            'updated_at' => null,
            'has_website' => false,
            'has_logo' => false,
        ];
    }
}



    /**
     * Get active organizations
     */
    public function getActive(Request $request): JsonResponse
    {
        $limit = min($request->get('limit', 50), 100);
        
        try {
            $organizations = Organization::withCount([
                    'datasets as total_datasets' => function($q) {
                        $q->where('approval_status', 'approved');
                    },
                    'infografis as total_infografis' => function($q) {
                        $q->where('is_public', true);
                    }
                ])
                ->having('total_datasets', '>', 0)
                ->orHaving('total_infografis', '>', 0)
                ->orderBy('name', 'asc')
                ->limit($limit)
                ->get()
                ->map(function($organization) {
                    $organization->logo_url = $organization->logo ? 
                        asset('storage/' . $organization->logo) : null;
                    return $organization;
                });

            return $this->successResponse($organizations, 'Active organizations retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Organization getActive API error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve active organizations', 500);
        }
    }

    /**
     * Get organization statistics
     */
    public function getStatistics(): JsonResponse
    {
        try {
            $stats = Cache::remember('organization_statistics', 3600, function() {
                return [
                    'total_organizations' => Organization::count(),
                    'organizations_this_month' => Organization::whereMonth('created_at', now()->month)->count(),
                    'organizations_with_website' => Organization::whereNotNull('website')->count(),
                    'organizations_with_logo' => Organization::whereNotNull('logo')->count(),
                    'most_active_organizations' => Organization::withCount([
                            'datasets as total_content' => function($q) {
                                $q->where('approval_status', 'approved');
                            }
                        ])
                        ->having('total_content', '>', 0)
                        ->orderBy('total_content', 'desc')
                        ->limit(10)
                        ->get()
                        ->map(function($org) {
                            $org->logo_url = $org->logo ? asset('storage/' . $org->logo) : null;
                            return $org;
                        }),
                    'organizations_by_content_count' => [
                        'high' => Organization::withCount([
                            'datasets as total_datasets' => function($q) {
                                $q->where('approval_status', 'approved');
                            }
                        ])->having('total_datasets', '>=', 10)->count(),
                        'medium' => Organization::withCount([
                            'datasets as total_datasets' => function($q) {
                                $q->where('approval_status', 'approved');
                            }
                        ])->having('total_datasets', '>=', 5)->having('total_datasets', '<', 10)->count(),
                        'low' => Organization::withCount([
                            'datasets as total_datasets' => function($q) {
                                $q->where('approval_status', 'approved');
                            }
                        ])->having('total_datasets', '>', 0)->having('total_datasets', '<', 5)->count(),
                    ]
                ];
            });

            return $this->successResponse($stats, 'Organization statistics retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Organization getStatistics API error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve organization statistics', 500);
        }
    }

    /**
     * Get organization datasets
     */
    public function getDatasets(Organization $organization, Request $request): JsonResponse
    {
        $perPage = min($request->get('per_page', $this->defaultLimit), $this->maxLimit);
        
        try {
            $datasets = Dataset::whereHas('user', function($q) use ($organization) {
                    $q->where('organization_id', $organization->id);
                })
                ->where('approval_status', 'approved')
                ->with(['user:id,name,organization_id'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            $datasets->getCollection()->transform(function ($dataset) {
                // Process tags
                if (is_string($dataset->tags)) {
                    $dataset->tags = json_decode($dataset->tags, true) ?? [];
                }
                if (!is_array($dataset->tags)) {
                    $dataset->tags = [];
                }
                
                // Remove sensitive paths
                unset($dataset->file_path);
                
                return $dataset;
            });

            return $this->successResponse([
                'datasets' => $datasets->items(),
                'pagination' => $this->getPaginationMeta($datasets),
                'organization' => [
                    'id' => $organization->id,
                    'name' => $organization->name,
                    'code' => $organization->code
                ],
            ], 'Organization datasets retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Organization getDatasets API error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve organization datasets', 500);
        }
    }

    /**
     * Get organization infografis
     */
    public function getInfografis(Organization $organization, Request $request): JsonResponse
    {
        $perPage = min($request->get('per_page', $this->defaultLimit), $this->maxLimit);
        
        try {
            $infografis = Infografis::whereHas('user', function($q) use ($organization) {
                    $q->where('organization_id', $organization->id);
                })
                ->where('is_public', true)
                ->with(['user:id,name,organization_id'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return $this->successResponse([
                'infografis' => $infografis->items(),
                'pagination' => $this->getPaginationMeta($infografis),
                'organization' => [
                    'id' => $organization->id,
                    'name' => $organization->name,
                    'code' => $organization->code
                ],
            ], 'Organization infografis retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Organization getInfografis API error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve organization infografis', 500);
        }
    }

    /**
     * Get organization mapsets
     */
    public function getMapsets(Organization $organization, Request $request): JsonResponse
    {
        $perPage = min($request->get('per_page', $this->defaultLimit), $this->maxLimit);
        
        try {
            $mapsets = Mapset::whereHas('user', function($q) use ($organization) {
                    $q->where('organization_id', $organization->id);
                })
                ->with(['user:id,name,organization_id'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return $this->successResponse([
                'mapsets' => $mapsets->items(),
                'pagination' => $this->getPaginationMeta($mapsets),
                'organization' => [
                    'id' => $organization->id,
                    'name' => $organization->name,
                    'code' => $organization->code
                ],
            ], 'Organization mapsets retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Organization getMapsets API error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve organization mapsets', 500);
        }
    }

    /**
     * Get organization visualisasi
     */
    public function getVisualisasi(Organization $organization, Request $request): JsonResponse
    {
        $perPage = min($request->get('per_page', $this->defaultLimit), $this->maxLimit);
        
        try {
            $visualisasi = Visualisasi::whereHas('user', function($q) use ($organization) {
                    $q->where('organization_id', $organization->id);
                })
                ->where('is_active', true)
                ->where('is_public', true)
                ->with(['user:id,name,organization_id'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return $this->successResponse([
                'visualisasi' => $visualisasi->items(),
                'pagination' => $this->getPaginationMeta($visualisasi),
                'organization' => [
                    'id' => $organization->id,
                    'name' => $organization->name,
                    'code' => $organization->code
                ],
            ], 'Organization visualisasi retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Organization getVisualisasi API error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve organization visualisasi', 500);
        }
    }

 
    /**
     * Get top contributing organizations
     */
    public function getTopContributors(Request $request): JsonResponse
    {
        $limit = min($request->get('limit', 10), 50);
        
        try {
            $organizations = Organization::withCount([
                    'datasets as total_datasets' => function($q) {
                        $q->where('approval_status', 'approved');
                    },
                    'infografis as total_infografis' => function($q) {
                        $q->where('is_public', true);
                    },
                    'mapsets as total_mapsets',
                    'visualisasi as total_visualisasi' => function($q) {
                        $q->where('is_active', true)->where('is_public', true);
                    }
                ])
                ->get()
                ->map(function($org) {
                    $org->total_contributions = 
                        $org->total_datasets + 
                        $org->total_infografis + 
                        $org->total_mapsets + 
                        $org->total_visualisasi;
                    
                    $org->logo_url = $org->logo ? asset('storage/' . $org->logo) : null;
                    
                    return $org;
                })
                ->sortByDesc('total_contributions')
                ->take($limit)
                ->values();

            return $this->successResponse($organizations, 'Top contributing organizations retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Organization getTopContributors API error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve top contributors', 500);
        }
    }

    /**
     * API endpoint for organization suggestions (like backend)
     */
    public function getSuggestions(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return $this->successResponse([], 'Suggestions retrieved successfully');
        }

        try {
            $organizations = Organization::where('name', 'ilike', "%{$query}%")
                ->select('id', 'name', 'code', 'logo')
                ->limit(10)
                ->get()
                ->map(function($org) {
                    $org->logo_url = $org->logo ? asset('storage/' . $org->logo) : null;
                    return $org;
                });

            return $this->successResponse($organizations, 'Suggestions retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Organization getSuggestions API error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve suggestions', 500);
        }
    }

    /**
     * Export organization catalog
     */
    public function exportCatalog(Request $request): JsonResponse
    {
        try {
            $organizations = Organization::withCount([
                    'datasets as total_datasets' => function($q) {
                        $q->where('approval_status', 'approved');
                    },
                    'infografis as total_infografis' => function($q) {
                        $q->where('is_public', true);
                    },
                    'mapsets as total_mapsets',
                    'visualisasi as total_visualisasi' => function($q) {
                        $q->where('is_active', true)->where('is_public', true);
                    }
                ])
                ->get()
                ->map(function($organization) {
                    return [
                        'id' => $organization->id,
                        'name' => $organization->name,
                        'code' => $organization->code,
                        'description' => $organization->description,
                        'website' => $organization->website,
                        'logo_url' => $organization->logo ? asset('storage/' . $organization->logo) : null,
                        'total_datasets' => $organization->total_datasets,
                        'total_infografis' => $organization->total_infografis,
                        'total_mapsets' => $organization->total_mapsets,
                        'total_visualisasi' => $organization->total_visualisasi,
                        'total_content' => $organization->total_datasets + 
                                         $organization->total_infografis + 
                                         $organization->total_mapsets + 
                                         $organization->total_visualisasi,
                        'created_at' => $organization->created_at,
                        'updated_at' => $organization->updated_at,
                    ];
                });

            return $this->successResponse([
                'catalog' => $organizations,
                'total_organizations' => $organizations->count(),
                'exported_at' => now()
            ], 'Organization catalog exported successfully');

        } catch (\Exception $e) {
            Log::error('Organization exportCatalog API error: ' . $e->getMessage());
            return $this->errorResponse('Failed to export catalog', 500);
        }
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
}