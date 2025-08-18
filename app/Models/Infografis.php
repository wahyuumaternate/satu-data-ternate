<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Infografis extends Model
{
    use HasFactory;

    protected $table = 'infografis';

    protected $fillable = [
        'user_id',
        'slug',
        'nama',
        'deskripsi',
        'gambar',
        'topic',
        'data_sources',
        'metodologi',
        'periode_data_mulai',
        'periode_data_selesai',
        'tags',
        'is_active',
        'is_public',
        'views',
        'downloads'
    ];

    protected $casts = [
        'data_sources' => 'array',
        'tags' => 'array',
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'views' => 'integer',
        'downloads' => 'integer',
        'periode_data_mulai' => 'date',
        'periode_data_selesai' => 'date',
    ];

    // Constants untuk enum topic
    const TOPICS = [
        'Ekonomi',
        'Infrastruktur',
        'Kemiskinan',
        'Kependudukan',
        'Kesehatan',
        'Lingkungan Hidup',
        'Pariwisata & Kebudayaan',
        'Pemerintah & Desa',
        'Pendidikan',
        'Sosial'
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Route key
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // Mutators
    public function setNamaAttribute($value)
    {
        $this->attributes['nama'] = $value;
        if (empty($this->attributes['slug'])) {
            $this->attributes['slug'] = Str::slug($value);
        }
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeByTopic($query, $topic)
    {
        return $query->where('topic', $topic);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('nama', 'ilike', "%{$search}%")
              ->orWhere('deskripsi', 'ilike', "%{$search}%")
              ->orWhereJsonContains('tags', $search);
        });
    }

    public function scopePopular($query)
    {
        return $query->orderBy('views', 'desc');
    }

    public function scopeMostDownloaded($query)
    {
        return $query->orderBy('downloads', 'desc');
    }

    // Methods
    public function incrementViews()
    {
        $this->increment('views');
    }

    public function incrementDownloads()
    {
        $this->increment('downloads');
    }

    public function getImageUrl()
    {
        return asset('storage/' . $this->gambar);
    }

    public function getPeriodeText()
    {
        if ($this->periode_data_mulai && $this->periode_data_selesai) {
            return $this->periode_data_mulai->format('d M Y') . ' - ' . $this->periode_data_selesai->format('d M Y');
        } elseif ($this->periode_data_mulai) {
            return 'Mulai ' . $this->periode_data_mulai->format('d M Y');
        } elseif ($this->periode_data_selesai) {
            return 'Sampai ' . $this->periode_data_selesai->format('d M Y');
        }
        return 'Periode tidak tersedia';
    }

    public function getTagsString()
    {
        return $this->tags ? implode(', ', $this->tags) : '';
    }

    public function getDataSourcesString()
    {
        return $this->data_sources ? implode(', ', $this->data_sources) : '';
    }
}