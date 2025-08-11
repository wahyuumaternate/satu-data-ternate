<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class Mapset extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'uuid',
        'nama',
        'deskripsi',
        'gambar',
        'topic',
        'is_visible',
        'is_active',
        'views',
        'geom'
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'is_active' => 'boolean',
        'views' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (!$model->uuid) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    // Relationship dengan User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope untuk filter berdasarkan topic
    public function scopeByTopic($query, $topic)
    {
        return $query->where('topic', $topic);
    }

    // Scope untuk mapset yang aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope untuk mapset yang visible
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    // Method untuk increment views
    public function incrementViews()
    {
        $this->increment('views');
    }

    // Method untuk mendapatkan koordinat dari geometry
    public function getCoordinates()
    {
        if (!$this->geom) {
            return null;
        }

        $result = DB::select("SELECT ST_AsGeoJSON(geom) as geojson FROM mapsets WHERE id = ?", [$this->id]);
        
        if (empty($result)) {
            return null;
        }

        return json_decode($result[0]->geojson, true);
    }

    // Method untuk set geometry dari GeoJSON
    public function setGeometryFromGeoJSON($geojson)
    {
        if (is_array($geojson)) {
            $geojson = json_encode($geojson);
        }

        DB::statement("UPDATE mapsets SET geom = ST_GeomFromGeoJSON(?) WHERE id = ?", [$geojson, $this->id]);
    }

    // Method untuk mendapatkan bounds dari geometry
    public function getBounds()
    {
        if (!$this->geom) {
            return null;
        }

        $result = DB::select("
            SELECT 
                ST_XMin(ST_Envelope(geom)) as min_lng,
                ST_YMin(ST_Envelope(geom)) as min_lat,
                ST_XMax(ST_Envelope(geom)) as max_lng,
                ST_YMax(ST_Envelope(geom)) as max_lat
            FROM mapsets 
            WHERE id = ?
        ", [$this->id]);

        if (empty($result)) {
            return null;
        }

        return [
            'min_lng' => $result[0]->min_lng,
            'min_lat' => $result[0]->min_lat,
            'max_lng' => $result[0]->max_lng,
            'max_lat' => $result[0]->max_lat,
        ];
    }

    // Method untuk search
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nama', 'ILIKE', "%{$search}%")
              ->orWhere('deskripsi', 'ILIKE', "%{$search}%");
        });
    }

    // Accessor untuk nama file gambar
    public function getGambarUrlAttribute()
    {
        if ($this->gambar) {
            return asset('storage/mapsets/' . $this->gambar);
        }
        return asset('assets/img/default-map.png');
    }

    // Static method untuk mendapatkan daftar topic
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

    // Method untuk mendapatkan warna berdasarkan topic
    public function getTopicColorAttribute()
    {
        $colors = [
            'Ekonomi' => '#28a745',
            'Infrastruktur' => '#6c757d',
            'Kemiskinan' => '#dc3545',
            'Kependudukan' => '#17a2b8',
            'Kesehatan' => '#fd7e14',
            'Lingkungan Hidup' => '#20c997',
            'Pariwisata & Kebudayaan' => '#e83e8c',
            'Pemerintah & Desa' => '#6f42c1',
            'Pendidikan' => '#007bff',
            'Sosial' => '#ffc107'
        ];

        return $colors[$this->topic] ?? '#6c757d';
    }

    // Method untuk mendapatkan icon berdasarkan topic
    public function getTopicIconAttribute()
    {
        $icons = [
            'Ekonomi' => 'bi-currency-dollar',
            'Infrastruktur' => 'bi-building',
            'Kemiskinan' => 'bi-house',
            'Kependudukan' => 'bi-people',
            'Kesehatan' => 'bi-heart-pulse',
            'Lingkungan Hidup' => 'bi-tree',
            'Pariwisata & Kebudayaan' => 'bi-camera',
            'Pemerintah & Desa' => 'bi-bank',
            'Pendidikan' => 'bi-book',
            'Sosial' => 'bi-people-fill'
        ];

        return $icons[$this->topic] ?? 'bi-geo-alt';
    }
}