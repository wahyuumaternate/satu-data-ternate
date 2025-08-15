<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MapsetFeature extends Model
{
    use HasFactory;

    protected $table = 'mapset_features';

    protected $fillable = [
        'mapset_id',
        'attributes',
    ];

    protected $casts = [
        'attributes' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // === RELATIONSHIPS ===

    /**
     * Get the mapset that owns this feature
     */
    public function mapset()
    {
        return $this->belongsTo(Mapset::class);
    }

    // === GEOMETRY METHODS ===

    /**
     * Get coordinates as GeoJSON
     */
    public function getCoordinates()
    {
        try {
            $result = DB::select("SELECT ST_AsGeoJSON(geom) as geojson FROM mapset_features WHERE id = ?", [$this->id]);
            
            if ($result && $result[0]->geojson) {
                return json_decode($result[0]->geojson, true);
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Error getting coordinates for feature ' . $this->id . ': ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get geometry type
     */
    public function getGeometryType()
    {
        try {
            $result = DB::select("SELECT ST_GeometryType(geom) as geom_type FROM mapset_features WHERE id = ?", [$this->id]);
            
            if ($result && $result[0]->geom_type) {
                return str_replace('ST_', '', $result[0]->geom_type);
            }
            
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Check if feature has geometry
     */
    public function hasGeometry()
    {
        try {
            $result = DB::select("SELECT 1 FROM mapset_features WHERE id = ? AND geom IS NOT NULL", [$this->id]);
            return !empty($result);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get feature bounds
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
                FROM mapset_features 
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
            Log::error('Error getting bounds for feature ' . $this->id . ': ' . $e->getMessage());
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
                FROM mapset_features 
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
            Log::error('Error getting center point for feature ' . $this->id . ': ' . $e->getMessage());
            return null;
        }
    }

    // === ATTRIBUTE METHODS ===

    /**
     * Get specific attribute
     */
    public function getFeatureAttribute($key, $default = null)
    {
        $attrs = $this->attributes ?? [];
        return $attrs[$key] ?? $default;
    }

    /**
     * Set attribute
     */
    public function setFeatureAttribute($key, $value)
    {
        $attrs = $this->attributes ?? [];
        $attrs[$key] = $value;
        $this->attributes = $attrs;
    }

    /**
     * Search in attributes
     */
    public function searchInAttributes($search)
    {
        if (!$this->attributes || empty($search)) {
            return false;
        }

        $attributesText = json_encode($this->attributes);
        return stripos($attributesText, $search) !== false;
    }

    // === SCOPES ===

    /**
     * Scope to get features with geometry
     */
    public function scopeWithGeometry($query)
    {
        return $query->whereNotNull('geom');
    }

    /**
     * Scope to get features by mapset
     */
    public function scopeByMapset($query, $mapsetId)
    {
        return $query->where('mapset_id', $mapsetId);
    }

    /**
     * Scope to search in attributes
     */
    public function scopeSearchAttributes($query, $search)
    {
        return $query->whereRaw("attributes::text ILIKE ?", ["%{$search}%"]);
    }

    // === UTILITY METHODS ===

    /**
     * Convert to GeoJSON Feature
     */
    public function toGeoJsonFeature()
    {
        $geometry = $this->getCoordinates();
        
        return [
            'type' => 'Feature',
            'properties' => array_merge($this->attributes ?? [], [
                'feature_id' => $this->id,
                'mapset_id' => $this->mapset_id,
                'created_at' => $this->created_at->toISOString(),
            ]),
            'geometry' => $geometry
        ];
    }

    /**
     * Update geometry from WKT
     */
    public function updateGeometry($wkt)
    {
        try {
            DB::statement("UPDATE mapset_features SET geom = ST_GeomFromText(?, 4326) WHERE id = ?", 
                [$wkt, $this->id]);
            return true;
        } catch (\Exception $e) {
            Log::error('Error updating geometry for feature ' . $this->id . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get geometry as WKT
     */
    public function getGeometryWKT()
    {
        try {
            $result = DB::select("SELECT ST_AsText(geom) as wkt FROM mapset_features WHERE id = ?", [$this->id]);
            
            if ($result && $result[0]->wkt) {
                return $result[0]->wkt;
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Error getting WKT for feature ' . $this->id . ': ' . $e->getMessage());
            return null;
        }
    }

    // === STATIC METHODS ===

    /**
     * Get features within bounds
     */
    public static function getWithinBounds($minLat, $minLng, $maxLat, $maxLng)
    {
        return static::whereRaw("ST_Intersects(geom, ST_MakeEnvelope(?, ?, ?, ?, 4326))", 
                     [$minLng, $minLat, $maxLng, $maxLat])
                     ->with('mapset')
                     ->get();
    }

    /**
     * Get features near point
     */
    public static function getNearPoint($lat, $lng, $radiusKm = 10)
    {
        return static::whereRaw("ST_DWithin(geom, ST_SetSRID(ST_MakePoint(?, ?), 4326), ?)", 
                     [$lng, $lat, $radiusKm * 1000]) // Convert km to meters
                     ->with('mapset')
                     ->get();
    }

    /**
     * Get features by geometry type
     */
    public static function getByGeometryType($geometryType)
    {
        return static::whereRaw("ST_GeometryType(geom) = ?", ['ST_' . strtoupper($geometryType)])
                     ->with('mapset')
                     ->get();
    }
}