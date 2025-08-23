<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Infografis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InfografisApiController extends Controller
{
    /**
     * Transform infografis collection to include image URLs
     * 
     * @param \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection|array $infografis
     * @return array
     */
    private function transformInfografisCollection($infografis)
    {
        // Convert to collection if it's an array
        if (is_array($infografis)) {
            $infografis = collect($infografis);
        }

        return $infografis->map(function ($item) {
            return $this->transformInfografis($item);
        })->toArray();
    }

    /**
     * Transform single infografis to include image URL
     * 
     * @param \App\Models\Infografis|array $infografis
     * @return array
     */
    private function transformInfografis($infografis)
    {
        // Handle both model instances and arrays
        if (is_array($infografis)) {
            $data = $infografis;
            $gambar = $data['gambar'] ?? null;
        } else {
            $data = $infografis->toArray();
            $gambar = $infografis->gambar;
        }
        
        // Add image URL
        $data['gambar_url'] = $gambar 
            ? asset('storage/' . $gambar)
            : null;
            
        return $data;
    }

    /**
     * Display a listing of the resource.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Infografis::with('user.organization:id,name,slug');

            // Hanya tampilkan infografis yang aktif dan publik
            $query->active()->public();

            // Filter berdasarkan topic
            if ($request->filled('topic')) {
                $query->byTopic($request->topic);
            }

            // Search
            if ($request->filled('search')) {
                $query->search($request->search);
            }

            // Filter berdasarkan tags
            if ($request->filled('tag')) {
                $query->whereJsonContains('tags', $request->tag);
            }

            // Sort
            $sort = $request->get('sort', 'latest');
            switch ($sort) {
                case 'popular':
                    $query->popular();
                    break;
                case 'downloads':
                    $query->mostDownloaded();
                    break;
                case 'oldest':
                    $query->oldest();
                    break;
                case 'name':
                    $query->orderBy('nama', 'asc');
                    break;
                case 'latest':
                default:
                    $query->latest();
                    break;
            }

            // Pagination
            $perPage = $request->get('per_page', 12);
            $perPage = min($perPage, 100); // Limit maksimal 100 per page
            $infografis = $query->paginate($perPage)->withQueryString();

            // Data untuk filters
            $topics = $this->getTopicsWithCount();
            $popularTags = $this->getPopularTags();
            $stats = $this->getStats();
            
            // Featured/Popular infografis
            $featured = Infografis::active()
                ->public()
                ->popular()
                ->limit(6)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'infografis' => $this->transformInfografisCollection($infografis->items()),
                    'pagination' => [
                        'current_page' => $infografis->currentPage(),
                        'last_page' => $infografis->lastPage(),
                        'per_page' => $infografis->perPage(),
                        'total' => $infografis->total(),
                        'from' => $infografis->firstItem(),
                        'to' => $infografis->lastItem(),
                        'has_more_pages' => $infografis->hasMorePages(),
                    ],
                    'filters' => [
                        'topics' => $topics,
                        'popular_tags' => $popularTags,
                        'current_filters' => [
                            'topic' => $request->get('topic'),
                            'search' => $request->get('search'),
                            'tag' => $request->get('tag'),
                            'sort' => $sort
                        ]
                    ],
                    'stats' => $stats,
                    'featured' => $this->transformInfografisCollection($featured)
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data infografis.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     * 
     * @param Infografis $infografis
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Infografis $infografis)
    {
        try {
            // Pastikan infografis aktif dan publik
            if (!$infografis->is_active || !$infografis->is_public) {
                return response()->json([
                    'success' => false,
                    'message' => 'Infografis tidak ditemukan atau tidak dapat diakses.'
                ], 404);
            }

            // Increment views
            $infografis->incrementViews();

            // Load user relationship
            $infografis->load('user.organization:id,name,slug');

            // Similar infografis based on topic
            $similar = Infografis::active()
                ->public()
                ->where('id', '!=', $infografis->id)
                ->where('topic', $infografis->topic)
                ->latest()
                ->limit(4)
                ->get();

            // Related by tags
            $related = collect();
            if ($infografis->tags && count($infografis->tags) > 0) {
                $related = Infografis::active()
                    ->public()
                    ->where('id', '!=', $infografis->id)
                    ->where(function($query) use ($infografis) {
                        foreach ($infografis->tags as $tag) {
                            $query->orWhereJsonContains('tags', $tag);
                        }
                    })
                    ->latest()
                    ->limit(4)
                    ->get();
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'infografis' => $this->transformInfografis($infografis),
                    'similar' => $this->transformInfografisCollection($similar),
                    'related' => $this->transformInfografisCollection($related),
                    'metadata' => [
                        'views' => $infografis->views,
                        'downloads' => $infografis->downloads,
                        'created_at' => $infografis->created_at,
                        'updated_at' => $infografis->updated_at,
                        'periode_text' => $infografis->getPeriodeText(),
                        'data_sources_string' => $infografis->getDataSourcesString(),
                        'tags_string' => $infografis->getTagsString(),
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data infografis.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * API endpoint for search suggestions
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function suggestions(Request $request)
    {
        try {
            $query = $request->get('q');
            
            if (!$query || strlen($query) < 2) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            $suggestions = Infografis::where('nama', 'ilike', "%{$query}%")
                ->active()
                ->public()
                ->select('id', 'nama', 'slug', 'topic')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $suggestions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil saran pencarian.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get infografis by topic
     * 
     * @param string $topic
     * @return \Illuminate\Http\JsonResponse
     */
    public function byTopic($topic)
    {
        try {
            $infografis = Infografis::with('user')
                ->byTopic($topic)
                ->active()
                ->public()
                ->latest()
                ->paginate(12);

            return response()->json([
                'success' => true,
                'data' => [
                    'infografis' => $this->transformInfografisCollection($infografis->items()),
                    'pagination' => [
                        'current_page' => $infografis->currentPage(),
                        'last_page' => $infografis->lastPage(),
                        'per_page' => $infografis->perPage(),
                        'total' => $infografis->total(),
                        'from' => $infografis->firstItem(),
                        'to' => $infografis->lastItem(),
                        'has_more_pages' => $infografis->hasMorePages(),
                    ],
                    'topic' => $topic
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data infografis berdasarkan topik.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * API search endpoint
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        try {
            $query = $request->get('q');
            $topic = $request->get('topic');
            $limit = $request->get('limit', 20);
            $limit = min($limit, 100); // Limit maksimal 100

            $infografisQuery = Infografis::with('user')
                ->active()
                ->public();

            if ($query) {
                $infografisQuery->search($query);
            }

            if ($topic) {
                $infografisQuery->byTopic($topic);
            }

            $infografis = $infografisQuery->latest()->limit($limit)->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'infografis' => $this->transformInfografisCollection($infografis),
                    'total' => $infografis->count(),
                    'query' => $query,
                    'topic' => $topic,
                    'limit' => $limit
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat melakukan pencarian.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get all available topics
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function topics()
    {
        try {
            $topics = $this->getTopicsWithCount();

            return response()->json([
                'success' => true,
                'data' => $topics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data topik.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get popular tags
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function tags()
    {
        try {
            $tags = $this->getPopularTags();

            return response()->json([
                'success' => true,
                'data' => $tags
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data tag.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get statistics
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats()
    {
        try {
            $stats = $this->getStats();

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil statistik.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get topics with count
     * 
     * @return array
     */
    private function getTopicsWithCount()
    {
        return DB::table('infografis')
            ->select('topic', DB::raw('count(*) as count'))
            ->where('is_active', true)
            ->where('is_public', true)
            ->whereNotNull('topic')
            ->groupBy('topic')
            ->orderBy('count', 'desc')
            ->pluck('count', 'topic')
            ->toArray();
    }

    /**
     * Get popular tags
     * 
     * @param int $limit
     * @return array
     */
    private function getPopularTags($limit = 20)
    {
        $tags = DB::table('infografis')
            ->where('is_active', true)
            ->where('is_public', true)
            ->whereNotNull('tags')
            ->pluck('tags');

        $allTags = [];
        foreach ($tags as $tagJson) {
            $tagArray = json_decode($tagJson, true);
            if (is_array($tagArray)) {
                $allTags = array_merge($allTags, $tagArray);
            }
        }

        $tagCounts = array_count_values($allTags);
        arsort($tagCounts);

        return array_slice($tagCounts, 0, $limit, true);
    }

    /**
     * Get statistics
     * 
     * @return array
     */
    private function getStats()
    {
        return [
            'total' => Infografis::active()->public()->count(),
            'total_views' => Infografis::active()->public()->sum('views'),
            'total_downloads' => Infografis::active()->public()->sum('downloads'),
            'topics_count' => Infografis::active()->public()->distinct('topic')->count('topic'),
            'this_month' => Infografis::active()->public()->whereMonth('created_at', now()->month)->count(),
        ];
    }
}