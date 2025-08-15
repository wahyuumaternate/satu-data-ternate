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
        'nama',
        'deskripsi',
        'gambar',
        'dbf_attributes',
        'topic',
        'is_visible',
        'is_active',
        'views',
    ];

    protected $casts = [
        'dbf_attributes' => 'array',
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
              ->orWhereRaw("dbf_attributes::text ILIKE ?", ["%{$search}%"]);
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

    // === GEOMETRY METHODS ===

    /**
     * Get coordinates as GeoJSON
     */
    public function getCoordinates()
    {
        try {
            $result = DB::select("SELECT ST_AsGeoJSON(geom) as geojson FROM mapsets WHERE id = ?", [$this->id]);
            
            if ($result && $result[0]->geojson) {
                return json_decode($result[0]->geojson, true);
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Error getting coordinates for mapset ' . $this->id . ': ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get geometry bounds
     */
    public function getBounds()
    {
        try {
            $result = DB::select("
                SELECT 
                    ST_XMin(ST_Envelope(geom)) as min_lng,
                    ST_YMin(ST_Envelope(geom)) as min_lat,
                    ST_XMax(ST_Envelope(geom)) as max_lng,
                    ST_YMax(ST_Envelope(geom)) as max_lat
                FROM mapsets 
                WHERE id = ? AND geom IS NOT NULL
            ", [$this->id]);
            
            if ($result && $result[0]) {
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
     * Get geometry center point
     */
    public function getCenterPoint()
    {
        try {
            $result = DB::select("
                SELECT 
                    ST_X(ST_Centroid(geom)) as lng,
                    ST_Y(ST_Centroid(geom)) as lat
                FROM mapsets 
                WHERE id = ? AND geom IS NOT NULL
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
            $result = DB::select("SELECT 1 FROM mapsets WHERE id = ? AND geom IS NOT NULL", [$this->id]);
            return !empty($result);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get geometry type
     */
    public function getGeometryType()
    {
        try {
            $result = DB::select("SELECT ST_GeometryType(geom) as geom_type FROM mapsets WHERE id = ?", [$this->id]);
            
            if ($result && $result[0]->geom_type) {
                return str_replace('ST_', '', $result[0]->geom_type);
            }
            
            return null;
        } catch (\Exception $e) {
            return null;
        }
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
            'Ekonomi' => 'primary',
            'Infrastruktur' => 'secondary',
            'Kemiskinan' => 'warning',
            'Kependudukan' => 'info',
            'Kesehatan' => 'success',
            'Lingkungan Hidup' => 'success',
            'Pariwisata & Kebudayaan' => 'purple',
            'Pemerintah & Desa' => 'dark',
            'Pendidikan' => 'primary',
            'Sosial' => 'info'
        ];

        return $colors[$this->topic] ?? 'secondary';
    }

    /**
     * Get topic icon
     */
    public function getTopicIcon()
    {
        $icons = [
            'Ekonomi' => 'fas fa-chart-line',
            'Infrastruktur' => 'fas fa-road',
            'Kemiskinan' => 'fas fa-hand-holding-heart',
            'Kependudukan' => 'fas fa-users',
            'Kesehatan' => 'fas fa-heartbeat',
            'Lingkungan Hidup' => 'fas fa-leaf',
            'Pariwisata & Kebudayaan' => 'fas fa-camera',
            'Pemerintah & Desa' => 'fas fa-landmark',
            'Pendidikan' => 'fas fa-graduation-cap',
            'Sosial' => 'fas fa-hands-helping'
        ];

        return $icons[$this->topic] ?? 'fas fa-map';
    }

    /**
     * Search in DBF attributes
     */
    public function searchInAttributes($search)
    {
        if (!$this->dbf_attributes || empty($search)) {
            return false;
        }

        $attributesText = json_encode($this->dbf_attributes);
        return stripos($attributesText, $search) !== false;
    }

    /**
     * Get specific DBF attribute
     */
    public function getDbfAttribute($key, $default = null)
    {
        return $this->dbf_attributes[$key] ?? $default;
    }

    /**
     * Set DBF attribute
     */
    public function setDbfAttribute($key, $value)
    {
        $attributes = $this->dbf_attributes ?? [];
        $attributes[$key] = $value;
        $this->dbf_attributes = $attributes;
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
     * Convert to GeoJSON Feature
     */
    public function toGeoJsonFeature()
    {
        $geometry = $this->getCoordinates();
        
        return [
            'type' => 'Feature',
            'properties' => [
                'id' => $this->id,
                'uuid' => $this->uuid,
                'nama' => $this->nama,
                'deskripsi' => $this->deskripsi,
                'topic' => $this->topic,
                'views' => $this->views,
                'is_visible' => $this->is_visible,
                'created_at' => $this->created_at->toISOString(),
                'dbf_attributes' => $this->dbf_attributes,
            ],
            'geometry' => $geometry
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
            'with_geometry' => $query->whereNotNull('geom')->count(),
            'without_geometry' => $query->whereNull('geom')->count(),
        ];
    }

    /**
     * Get popular mapsets
     */
    public static function getPopular($limit = 10)
    {
        return static::visible()
                    ->active()
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
                    ->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Search mapsets
     */
    public static function searchMapsets($query, $filters = [])
    {
        $builder = static::visible()->active();

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
                    ->whereRaw("ST_Intersects(geom, ST_MakeEnvelope(?, ?, ?, ?, 4326))", 
                             [$minLng, $minLat, $maxLng, $maxLat])
                    ->get();
    }

    /**
     * Get mapsets near point
     */
    public static function getNearPoint($lat, $lng, $radiusKm = 10)
    {
        return static::visible()
                    ->active()
                    ->whereRaw("ST_DWithin(geom, ST_SetSRID(ST_MakePoint(?, ?), 4326), ?)", 
                             [$lng, $lat, $radiusKm * 1000]) // Convert km to meters
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