<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use App\Models\Mapset;
use App\Models\Infografis;
use App\Models\Visualisasi;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class GlobalSearchController extends Controller
{
    

    /**
     * Global search across all content types
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function globalSearch(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'q' => 'nullable|string|max:255',
                'type' => 'nullable|string|in:dataset,mapset,infografis,visualisasi,all',
                'topic' => 'nullable|string|max:100',
                'tags' => 'nullable|string|max:500',
                'organization' => 'nullable|string|max:100',
                'per_page' => 'nullable|integer|min:1|max:50',
                'page' => 'nullable|integer|min:1',
                'sort_by' => 'nullable|string|in:relevance,date,popularity,title',
                'sort_order' => 'nullable|string|in:asc,desc',
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date',
                'is_featured' => 'nullable|boolean'
            ]);

            $query = $request->input('q', '');
            $type = $request->input('type', 'all');
            $topic = $request->input('topic');
            $tags = $request->input('tags');
            $organization = $request->input('organization');
            $perPage = $request->input('per_page', 12);
            $sortBy = $request->input('sort_by', 'relevance');
            $sortOrder = $request->input('sort_order', 'desc');
            $dateFrom = $request->input('date_from');
            $dateTo = $request->input('date_to');
            $isFeatured = $request->input('is_featured');

            // Parse tags if provided
            $tagArray = $tags ? array_map('trim', explode(',', $tags)) : [];

            $results = [];
            $totalCounts = [];

            // Search based on type
            if ($type === 'all') {
                $results['datasets'] = $this->searchDatasets($query, $topic, $tagArray, $organization, $dateFrom, $dateTo, $isFeatured, $perPage, $sortBy, $sortOrder);
                $results['mapsets'] = $this->searchMapsets($query, $topic, $tagArray, $organization, $dateFrom, $dateTo, $isFeatured, $perPage, $sortBy, $sortOrder);
                $results['infografis'] = $this->searchInfografis($query, $topic, $tagArray, $organization, $dateFrom, $dateTo, $isFeatured, $perPage, $sortBy, $sortOrder);
                $results['visualisasi'] = $this->searchVisualisasi($query, $topic, $tagArray, $organization, $dateFrom, $dateTo, $isFeatured, $perPage, $sortBy, $sortOrder);

                // Get total counts for each type
                $totalCounts = [
                    'datasets' => $results['datasets']->total(),
                    'mapsets' => $results['mapsets']->total(),
                    'infografis' => $results['infografis']->total(),
                    'visualisasi' => $results['visualisasi']->total(),
                ];
            } else {
                // Search specific type
                switch ($type) {
                    case 'dataset':
                        $results['datasets'] = $this->searchDatasets($query, $topic, $tagArray, $organization, $dateFrom, $dateTo, $isFeatured, $perPage, $sortBy, $sortOrder);
                        $totalCounts['datasets'] = $results['datasets']->total();
                        break;
                    case 'mapset':
                        $results['mapsets'] = $this->searchMapsets($query, $topic, $tagArray, $organization, $dateFrom, $dateTo, $isFeatured, $perPage, $sortBy, $sortOrder);
                        $totalCounts['mapsets'] = $results['mapsets']->total();
                        break;
                    case 'infografis':
                        $results['infografis'] = $this->searchInfografis($query, $topic, $tagArray, $organization, $dateFrom, $dateTo, $isFeatured, $perPage, $sortBy, $sortOrder);
                        $totalCounts['infografis'] = $results['infografis']->total();
                        break;
                    case 'visualisasi':
                        $results['visualisasi'] = $this->searchVisualisasi($query, $topic, $tagArray, $organization, $dateFrom, $dateTo, $isFeatured, $perPage, $sortBy, $sortOrder);
                        $totalCounts['visualisasi'] = $results['visualisasi']->total();
                        break;
                }
            }

            // Get search metadata
            $metadata = $this->getSearchMetadata($query, $type, $topic, $tagArray);

            return response()->json([
                'success' => true,
                'message' => 'Search completed successfully',
                'data' => [
                    'results' => $results,
                    'metadata' => $metadata,
                    'counts' => $totalCounts,
                    'total_results' => array_sum($totalCounts),
                    'search_params' => [
                        'query' => $query,
                        'type' => $type,
                        'topic' => $topic,
                        'tags' => $tagArray,
                        'organization' => $organization,
                        'per_page' => $perPage,
                        'sort_by' => $sortBy,
                        'sort_order' => $sortOrder,
                        'filters_applied' => $this->getAppliedFilters($request)
                    ]
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid search parameters',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Search datasets
     */
    private function searchDatasets($query, $topic, $tags, $organization, $dateFrom, $dateTo, $isFeatured, $perPage, $sortBy, $sortOrder)
    {
        $builder = Dataset::query()
            ->with(['user'])
            ->where('is_public', true)
            ->where('publish_status', 'published')
            ->where('approval_status', 'approved');

        // Apply search query
        if (!empty($query)) {
            $builder->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%")
                  ->orWhere('data_source', 'LIKE', "%{$query}%")
                  ->orWhere('notes', 'LIKE', "%{$query}%")
                  ->orWhereJsonContains('tags', $query);
            });
        }

        // Apply filters
        $this->applyCommonFilters($builder, $topic, $tags, $organization, $dateFrom, $dateTo, $isFeatured);

        // Apply sorting
        $this->applySorting($builder, $sortBy, $sortOrder, 'dataset');

        return $builder->paginate($perPage);
    }

    /**
     * Search mapsets
     */
    private function searchMapsets($query, $topic, $tags, $organization, $dateFrom, $dateTo, $isFeatured, $perPage, $sortBy, $sortOrder)
    {
        $builder = Mapset::query()
            ->with(['user'])
            ->where('is_visible', true)
            ->where('is_active', true);

        // Apply search query
        if (!empty($query)) {
            $builder->where(function ($q) use ($query) {
                $q->where('nama', 'LIKE', "%{$query}%")
                  ->orWhere('deskripsi', 'LIKE', "%{$query}%");
            });
        }

        // Apply filters
        $this->applyCommonFilters($builder, $topic, $tags, $organization, $dateFrom, $dateTo, $isFeatured);

        // Apply sorting
        $this->applySorting($builder, $sortBy, $sortOrder, 'mapset');

        return $builder->paginate($perPage);
    }

    /**
     * Search infografis
     */
    private function searchInfografis($query, $topic, $tags, $organization, $dateFrom, $dateTo, $isFeatured, $perPage, $sortBy, $sortOrder)
    {
        $builder = Infografis::query()
            ->with(['user'])
            ->where('is_public', true)
            ->where('is_active', true);

        // Apply search query
        if (!empty($query)) {
            $builder->where(function ($q) use ($query) {
                $q->where('nama', 'LIKE', "%{$query}%")
                  ->orWhere('deskripsi', 'LIKE', "%{$query}%")
                  ->orWhere('metodologi', 'LIKE', "%{$query}%")
                  ->orWhereJsonContains('tags', $query);
            });
        }

        // Apply filters
        $this->applyCommonFilters($builder, $topic, $tags, $organization, $dateFrom, $dateTo, $isFeatured);

        // Apply sorting for infografis
        $this->applySorting($builder, $sortBy, $sortOrder, 'infografis');

        return $builder->paginate($perPage);
    }

    /**
     * Search visualisasi
     */
    private function searchVisualisasi($query, $topic, $tags, $organization, $dateFrom, $dateTo, $isFeatured, $perPage, $sortBy, $sortOrder)
    {
        $builder = Visualisasi::query()
            ->with(['user'])
            ->where('is_public', true)
            ->where('is_active', true);

        // Apply search query
        if (!empty($query)) {
            $builder->where(function ($q) use ($query) {
                $q->where('nama', 'LIKE', "%{$query}%")
                  ->orWhere('deskripsi', 'LIKE', "%{$query}%");
            });
        }

        // Apply filters
        $this->applyCommonFilters($builder, $topic, $tags, $organization, $dateFrom, $dateTo, $isFeatured);

        // Apply sorting
        $this->applySorting($builder, $sortBy, $sortOrder, 'visualisasi');

        return $builder->paginate($perPage);
    }

    /**
     * Apply common filters to query builder
     */
    private function applyCommonFilters($builder, $topic, $tags, $organization, $dateFrom, $dateTo, $isFeatured)
    {
        // Filter by topic
        if (!empty($topic)) {
            $builder->where('topic', $topic);
        }

        // Filter by tags (for datasets and infografis that have JSON tags)
        if (!empty($tags)) {
            foreach ($tags as $tag) {
                $builder->whereJsonContains('tags', $tag);
            }
        }

        // Filter by organization/user
        if (!empty($organization)) {
            $builder->whereHas('user', function ($q) use ($organization) {
                $q->where('organization', 'LIKE', "%{$organization}%");
            });
        }

        // Filter by date range
        if (!empty($dateFrom)) {
            $builder->where('created_at', '>=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $builder->where('created_at', '<=', $dateTo . ' 23:59:59');
        }

        // Note: is_featured filter might not be applicable to all models
        // Add only if the model has this field
    }

    /**
     * Apply sorting to query builder
     */
    private function applySorting($builder, $sortBy, $sortOrder, $type)
    {
        switch ($sortBy) {
            case 'date':
                $builder->orderBy('created_at', $sortOrder);
                break;
            case 'popularity':
                if ($type === 'dataset') {
                    $builder->orderBy('download_count', $sortOrder);
                } elseif ($type === 'mapset' || $type === 'infografis' || $type === 'visualisasi') {
                    $builder->orderBy('views', $sortOrder);
                } else {
                    $builder->orderBy('created_at', $sortOrder);
                }
                break;
            case 'title':
                $fieldName = ($type === 'dataset') ? 'title' : 'nama';
                $builder->orderBy($fieldName, $sortOrder);
                break;
            case 'relevance':
            default:
                // For relevance, prioritize recently updated content
                $builder->orderBy('updated_at', 'desc');
                break;
        }
    }

    /**
     * Get search metadata
     */
    private function getSearchMetadata($query, $type, $topic, $tags)
    {
        $metadata = [
            'available_topics' => $this->getAvailableTopics(),
            'popular_tags' => $this->getPopularTags(),
            'available_organizations' => $this->getAvailableOrganizations(),
            'search_suggestions' => []
        ];

        // Add search suggestions if query is provided
        if (!empty($query)) {
            $metadata['search_suggestions'] = $this->getSearchSuggestions($query);
        }

        return $metadata;
    }

    /**
     * Get available topics across all content types
     */
    private function getAvailableTopics()
    {
        return Cache::remember('global_search_topics', 3600, function () {
            $topics = collect();
            
            $topics = $topics->merge(Dataset::where('is_public', true)->where('publish_status', 'published')->distinct()->pluck('topic'));
            $topics = $topics->merge(Mapset::where('is_visible', true)->distinct()->pluck('topic'));
            $topics = $topics->merge(Infografis::where('is_public', true)->distinct()->pluck('topic'));
            $topics = $topics->merge(Visualisasi::where('is_public', true)->distinct()->pluck('topic'));
            
            return $topics->filter()->unique()->sort()->values()->toArray();
        });
    }

    /**
     * Get popular tags across all content types
     */
    private function getPopularTags()
    {
        return Cache::remember('global_search_popular_tags', 3600, function () {
            $tags = collect();
            
            // Get tags from datasets (JSON field)
            $datasetTags = Dataset::where('is_public', true)
                ->where('publish_status', 'published')
                ->whereNotNull('tags')
                ->pluck('tags')
                ->flatMap(function ($tagArray) {
                    if (is_array($tagArray)) {
                        return $tagArray;
                    }
                    $decoded = json_decode($tagArray, true);
                    return $decoded ? $decoded : [];
                });
            
            // Get tags from infografis (JSON field)
            $infografisTags = Infografis::where('is_public', true)
                ->whereNotNull('tags')
                ->pluck('tags')
                ->flatMap(function ($tagArray) {
                    if (is_array($tagArray)) {
                        return $tagArray;
                    }
                    $decoded = json_decode($tagArray, true);
                    return $decoded ? $decoded : [];
                });
            
            $tags = $tags->merge($datasetTags)->merge($infografisTags);
            
            return $tags->countBy()->sortDesc()->take(20)->keys()->toArray();
        });
    }

    /**
     * Get available organizations
     */
    private function getAvailableOrganizations()
    {
        return Cache::remember('global_search_organizations', 3600, function () {
            return Organization::select('name', 'slug')
                ->orderBy('name')
                ->get()
                ->toArray();
        });
    }

    /**
     * Get search suggestions based on query
     */
    private function getSearchSuggestions($query)
    {
        if (strlen($query) < 3) {
            return [];
        }

        $suggestions = collect();
        
        // Get suggestions from dataset titles
        $datasetSuggestions = Dataset::where('is_public', true)
            ->where('publish_status', 'published')
            ->where('title', 'LIKE', "%{$query}%")
            ->limit(3)
            ->pluck('title');
        
        // Get suggestions from mapset names
        $mapsetSuggestions = Mapset::where('is_visible', true)
            ->where('nama', 'LIKE', "%{$query}%")
            ->limit(3)
            ->pluck('nama');
            
        // Get suggestions from infografis names
        $infografisSuggestions = Infografis::where('is_public', true)
            ->where('nama', 'LIKE', "%{$query}%")
            ->limit(3)
            ->pluck('nama');
            
        // Get suggestions from visualisasi names
        $visualisasiSuggestions = Visualisasi::where('is_public', true)
            ->where('nama', 'LIKE', "%{$query}%")
            ->limit(3)
            ->pluck('nama');

        $suggestions = $suggestions
            ->merge($datasetSuggestions)
            ->merge($mapsetSuggestions)
            ->merge($infografisSuggestions)
            ->merge($visualisasiSuggestions);

        return $suggestions->unique()->take(10)->values()->toArray();
    }

    /**
     * Get applied filters summary
     */
    private function getAppliedFilters($request)
    {
        $filters = [];
        
        if ($request->filled('topic')) {
            $filters['topic'] = $request->input('topic');
        }
        
        if ($request->filled('tags')) {
            $filters['tags'] = array_map('trim', explode(',', $request->input('tags')));
        }
        
        if ($request->filled('organization')) {
            $filters['organization'] = $request->input('organization');
        }
        
        if ($request->filled('date_from')) {
            $filters['date_from'] = $request->input('date_from');
        }
        
        if ($request->filled('date_to')) {
            $filters['date_to'] = $request->input('date_to');
        }
        
        if ($request->filled('is_featured')) {
            $filters['is_featured'] = $request->boolean('is_featured');
        }

        return $filters;
    }

    /**
     * Get quick search autocomplete suggestions
     */
    public function autocomplete(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100'
        ]);

        $query = $request->input('q');
        
        try {
            $suggestions = Cache::remember("autocomplete_{$query}", 300, function () use ($query) {
                $results = collect();
                
                // Get from datasets
                $datasets = Dataset::where('is_public', true)
                    ->where('publish_status', 'published')
                    ->where('title', 'LIKE', "%{$query}%")
                    ->limit(3)
                    ->select('title as title', 'slug', DB::raw("'dataset' as type"))
                    ->get();
                
                // Get from mapsets
                $mapsets = Mapset::where('is_visible', true)
                    ->where('nama', 'LIKE', "%{$query}%")
                    ->limit(3)
                    ->select('nama as title', 'slug', DB::raw("'mapset' as type"))
                    ->get();
                    
                // Get from infografis
                $infografis = Infografis::where('is_public', true)
                    ->where('nama', 'LIKE', "%{$query}%")
                    ->limit(3)
                    ->select('nama as title', 'slug', DB::raw("'infografis' as type"))
                    ->get();
                    
                // Get from visualisasi
                $visualisasi = Visualisasi::where('is_public', true)
                    ->where('nama', 'LIKE', "%{$query}%")
                    ->limit(3)
                    ->select('nama as title', 'slug', DB::raw("'visualisasi' as type"))
                    ->get();

                return $results
                    ->merge($datasets)
                    ->merge($mapsets)
                    ->merge($infografis)
                    ->merge($visualisasi)
                    ->take(10);
            });

            return response()->json([
                'success' => true,
                'data' => $suggestions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Autocomplete failed',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
}
}