<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Mapset extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'uuid',
        'slug',
        'nama',
        'deskripsi',
        'gambar',
        'topic',
        'is_visible',
        'is_active',
        'views',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'is_active' => 'boolean',
        'views' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
        });
    }
   
    // === RELATIONSHIPS ===

    /**
     * Get the user that owns the mapset
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the features associated with this mapset
     */
    public function features()
    {
        return $this->hasMany(MapsetFeature::class);
    }

    // === SCOPES ===

    /**
     * Scope to get only active mapsets
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get only visible mapsets
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * Scope to search mapsets
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('nama', 'like', "%{$search}%")
              ->orWhere('deskripsi', 'like', "%{$search}%")
              ->orWhereHas('features', function($featureQuery) use ($search) {
                  $featureQuery->whereRaw("attributes::text ILIKE ?", ["%{$search}%"]);
              });
        });
    }

    /**
     * Scope to filter by topic
     */
    public function scopeByTopic($query, $topic)
    {
        return $query->where('topic', $topic);
    }

    // === ACCESSORS & MUTATORS ===

    /**
     * Get the gambar URL
     */
    public function getGambarUrlAttribute()
    {
        if ($this->gambar) {
            return asset('storage/mapsets/' . $this->gambar);
        }
        return null;
    }

    /**
     * Get formatted topic
     */
    public function getFormattedTopicAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->topic));
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        if (!$this->is_active) {
            return 'Tidak Aktif';
        }
        
        return $this->is_visible ? 'Publik' : 'Privat';
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute()
    {
        if (!$this->is_active) {
            return 'danger';
        }
        
        return $this->is_visible ? 'success' : 'secondary';
    }

    // === GEOMETRY METHODS (UPDATED FOR NEW SCHEMA) ===

    /**
     * Get all coordinates as GeoJSON FeatureCollection
     */
    public function getCoordinates()
    {
        try {
            $features = $this->features()->whereNotNull('geom')->get();
            
            if ($features->isEmpty()) {
                return null;
            }

            $geojsonFeatures = [];
            foreach ($features as $feature) {
                $geojsonFeatures[] = $feature->toGeoJsonFeature();
            }

            return [
                'type' => 'FeatureCollection',
                'features' => $geojsonFeatures
            ];
        } catch (\Exception $e) {
            Log::error('Error getting coordinates for mapset ' . $this->id . ': ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get geometry bounds from all features
     */
    public function getBounds()
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
            ", [$this->id]);
            
            if ($result && $result[0] && $result[0]->min_lng !== null) {
                return [
                    [$result[0]->min_lat, $result[0]->min_lng],
                    [$result[0]->max_lat, $result[0]->max_lng]
                ];
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Error getting bounds for mapset ' . $this->id . ': ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get geometry center point from all features
     */
    public function getCenterPoint()
    {
        try {
            $result = DB::select("
                SELECT 
                    ST_X(ST_Centroid(ST_Collect(geom))) as lng,
                    ST_Y(ST_Centroid(ST_Collect(geom))) as lat
                FROM mapset_features 
                WHERE mapset_id = ? AND geom IS NOT NULL
            ", [$this->id]);
            
            if ($result && $result[0]) {
                return [
                    'lat' => (float) $result[0]->lat,
                    'lng' => (float) $result[0]->lng
                ];
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Error getting center point for mapset ' . $this->id . ': ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if mapset has geometry
     */
    public function hasGeometry()
    {
        try {
            return $this->features()->whereNotNull('geom')->exists();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get count of features with geometry
     */
    public function getGeometryCount()
    {
        try {
            return $this->features()->whereNotNull('geom')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get geometry types present in this mapset
     */
    public function getGeometryTypes()
    {
        try {
            $result = DB::select("
                SELECT DISTINCT ST_GeometryType(geom) as geom_type 
                FROM mapset_features 
                WHERE mapset_id = ? AND geom IS NOT NULL
            ", [$this->id]);
            
            return array_map(function($item) {
                return str_replace('ST_', '', $item->geom_type);
            }, $result);
        } catch (\Exception $e) {
            return [];
        }
    }

    // === FEATURE METHODS ===

    /**
     * Get features count
     */
    public function getFeaturesCount()
    {
        return $this->features()->count();
    }

    /**
     * Get features with geometry count
     */
    public function getFeaturesWithGeometryCount()
    {
        return $this->features()->whereNotNull('geom')->count();
    }

    /**
     * Search in features attributes
     */
    public function searchInFeaturesAttributes($search)
    {
        return $this->features()
                   ->whereRaw("attributes::text ILIKE ?", ["%{$search}%"])
                   ->exists();
    }

    // === UTILITY METHODS ===

    /**
     * Increment views count
     */
    public function incrementViews()
    {
        $this->increment('views');
    }

    /**
     * Get available topics
     */
    public static function getTopics()
    {
        return [
            'Ekonomi' => 'Ekonomi',
            'Infrastruktur' => 'Infrastruktur',
            'Kemiskinan' => 'Kemiskinan',
            'Kependudukan' => 'Kependudukan',
            'Kesehatan' => 'Kesehatan',
            'Lingkungan Hidup' => 'Lingkungan Hidup',
            'Pariwisata & Kebudayaan' => 'Pariwisata & Kebudayaan',
            'Pemerintah & Desa' => 'Pemerintah & Desa',
            'Pendidikan' => 'Pendidikan',
            'Sosial' => 'Sosial'
        ];
    }

    /**
     * Get topic color
     */
    public function getTopicColor()
    {
        $colors = [
            'Ekonomi' => '#0d6efd',
            'Infrastruktur' => '#6c757d',
            'Kemiskinan' => '#ffc107',
            'Kependudukan' => '#0dcaf0',
            'Kesehatan' => '#198754',
            'Lingkungan Hidup' => '#20c997',
            'Pariwisata & Kebudayaan' => '#6f42c1',
            'Pemerintah & Desa' => '#212529',
            'Pendidikan' => '#0d6efd',
            'Sosial' => '#0dcaf0'
        ];

        return $colors[$this->topic] ?? '#6c757d';
    }

    /**
     * Get topic icon
     */
    public function getTopicIcon()
    {
        $icons = [
            'Ekonomi' => 'bi bi-graph-up',
            'Infrastruktur' => 'bi bi-building',
            'Kemiskinan' => 'bi bi-heart',
            'Kependudukan' => 'bi bi-people',
            'Kesehatan' => 'bi bi-heart-pulse',
            'Lingkungan Hidup' => 'bi bi-tree',
            'Pariwisata & Kebudayaan' => 'bi bi-camera',
            'Pemerintah & Desa' => 'bi bi-building-gear',
            'Pendidikan' => 'bi bi-mortarboard',
            'Sosial' => 'bi bi-people-fill'
        ];

        return $icons[$this->topic] ?? 'bi bi-geo-alt';
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSize()
    {
        if (!$this->gambar) {
            return null;
        }

        $path = storage_path('app/public/mapsets/' . $this->gambar);
        
        if (!file_exists($path)) {
            return null;
        }

        $bytes = filesize($path);
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check if user can view this mapset
     */
    public function canBeViewedBy($user = null)
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->is_visible) {
            return true;
        }

        if (!$user) {
            return false;
        }

        return $this->user_id === $user->id;
    }

    /**
     * Check if user can edit this mapset
     */
    public function canBeEditedBy($user = null)
    {
        if (!$user) {
            return false;
        }

        return $this->user_id === $user->id;
    }

    /**
     * Get route key name for model binding
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    /**
     * Convert to GeoJSON FeatureCollection
     */
    public function toGeoJsonFeatureCollection()
    {
        $features = $this->features()->whereNotNull('geom')->get();
        
        $geojsonFeatures = [];
        foreach ($features as $feature) {
            $geojsonFeatures[] = $feature->toGeoJsonFeature();
        }
        
        return [
            'type' => 'FeatureCollection',
            'properties' => [
                'mapset_id' => $this->id,
                'mapset_uuid' => $this->uuid,
                'mapset_name' => $this->nama,
                'mapset_description' => $this->deskripsi,
                'mapset_topic' => $this->topic,
                'mapset_views' => $this->views,
                'mapset_is_visible' => $this->is_visible,
                'mapset_created_at' => $this->created_at->toISOString(),
                'features_count' => count($geojsonFeatures),
            ],
            'features' => $geojsonFeatures
        ];
    }

    
    // === STATIC METHODS ===

    /**
     * Get statistics for user mapsets
     */
    public static function getUserStatistics($userId)
    {
        $query = static::where('user_id', $userId)->where('is_active', true);
        
        return [
            'total' => $query->count(),
            'public' => $query->where('is_visible', true)->count(),
            'private' => $query->where('is_visible', false)->count(),
            'total_views' => $query->sum('views'),
            'by_topic' => $query->select('topic')
                               ->selectRaw('count(*) as count')
                               ->groupBy('topic')
                               ->pluck('count', 'topic')
                               ->toArray(),
            'with_geometry' => $query->whereHas('features', function($q) {
                                  $q->whereNotNull('geom');
                              })->count(),
            'total_features' => DB::table('mapset_features')
                                 ->join('mapsets', 'mapset_features.mapset_id', '=', 'mapsets.id')
                                 ->where('mapsets.user_id', $userId)
                                 ->where('mapsets.is_active', true)
                                 ->count(),
        ];
    }

    /**
     * Get popular mapsets
     */
    public static function getPopular($limit = 10)
    {
        return static::visible()
                    ->active()
                    ->with(['features', 'user'])
                    ->orderBy('views', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Get recent mapsets
     */
    public static function getRecent($limit = 10)
    {
        return static::visible()
                    ->active()
                    ->with(['features', 'user'])
                    ->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Search mapsets
     */
    public static function searchMapsets($query, $filters = [])
    {
        $builder = static::visible()->active()->with(['features', 'user']);

        if (!empty($query)) {
            $builder->search($query);
        }

        if (!empty($filters['topic'])) {
            $builder->byTopic($filters['topic']);
        }

        if (!empty($filters['user_id'])) {
            $builder->where('user_id', $filters['user_id']);
        }

        return $builder;
    }

    /**
     * Get mapsets within bounds
     */
    public static function getWithinBounds($minLat, $minLng, $maxLat, $maxLng)
    {
        return static::visible()
                    ->active()
                    ->whereHas('features', function($query) use ($minLat, $minLng, $maxLat, $maxLng) {
                        $query->whereRaw("ST_Intersects(geom, ST_MakeEnvelope(?, ?, ?, ?, 4326))", 
                                        [$minLng, $minLat, $maxLng, $maxLat]);
                    })
                    ->with(['features', 'user'])
                    ->get();
    }

    /**
     * Get mapsets near point
     */
    public static function getNearPoint($lat, $lng, $radiusKm = 10)
    {
        return static::visible()
                    ->active()
                    ->whereHas('features', function($query) use ($lat, $lng, $radiusKm) {
                        $query->whereRaw("ST_DWithin(geom, ST_SetSRID(ST_MakePoint(?, ?), 4326), ?)", 
                                        [$lng, $lat, $radiusKm * 1000]); // Convert km to meters
                    })
                    ->with(['features', 'user'])
                    ->get();
    }

    public function getStatusColor(): string
    {
        if ($this->is_active) {
            return 'success'; // hijau
        }
        return 'danger'; // merah
    }

    public function getStatusLabel(): string
    {
        return $this->is_active ? 'Aktif' : 'Nonaktif';
    }

    
}