<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Dataset extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'tags',
        'filename',
        'original_filename',
        'file_path',
        'file_size',
        'file_type',
        'headers',
        'data',
        'total_rows',
        'total_columns',
        'license',
        'topic',
        'sector',
        'responsible_person',
        'contact',
        'classification',
        'status',
        'data_source',
        'data_period',
        'update_frequency',
        'geographic_coverage',
        'publish_status',
        'is_public',
        'published_at',
        'user_id',
        'organization',
        'notes',
        'processing_log',
        'download_count',
        'view_count',
        // 🎯 TAMBAH KOLOM APPROVAL INI
        'approval_status',
        'approved_by',
        'approved_at',
        'approval_notes',
        'rejection_reason'
    ];

    protected $casts = [
        'headers' => 'array',
        'data' => 'array',
        'tags' => 'array',
        'processing_log' => 'array',
        'total_rows' => 'integer',
        'total_columns' => 'integer',
        'file_size' => 'integer',
        'download_count' => 'integer',
        'view_count' => 'integer',
        'is_public' => 'boolean',
        'published_at' => 'datetime',
        // 🎯 TAMBAH CAST UNTUK APPROVAL
        'approved_at' => 'datetime'
    ];

    protected $dates = [
        'published_at',
        'approved_at' // 🎯 TAMBAH INI
    ];

    // Approval status constants
    const APPROVAL_PENDING = 'pending';
    const APPROVAL_APPROVED = 'approved';
    const APPROVAL_REJECTED = 'rejected';
   
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Mutators
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        $this->attributes['slug'] = Str::slug($value, '-');
    }

    public function setTagsAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['tags'] = json_encode(array_filter(explode(',', $value)));
        } else {
            $this->attributes['tags'] = json_encode($value);
        }
    }

    // Accessors
    public function getFormattedFileSizeAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return $bytes . ' byte';
        } else {
            return '0 bytes';
        }
    }

    public function getTagsListAttribute()
    {
        return is_array($this->tags) ? implode(', ', $this->tags) : '';
    }

    public function getStatusBadgeAttribute()
    {
        $statusColors = [
            'draft' => 'secondary',
            'published' => 'success',
            'archived' => 'warning'
        ];

        $color = $statusColors[$this->publish_status] ?? 'secondary';
        return "<span class='badge bg-{$color}'>" . ucfirst($this->publish_status) . "</span>";
    }

    public function getClassificationBadgeAttribute()
    {
        $classificationColors = [
            'publik' => 'success',
            'internal' => 'warning',
            'terbatas' => 'danger',
            'rahasia' => 'dark'
        ];

        $color = $classificationColors[$this->classification] ?? 'secondary';
        return "<span class='badge bg-{$color}'>" . ucfirst($this->classification) . "</span>";
    }

    // 🎯 TAMBAH APPROVAL METHODS
    public function isApproved(): bool
    {
        return $this->approval_status === self::APPROVAL_APPROVED;
    }

    public function isPending(): bool
    {
        return $this->approval_status === self::APPROVAL_PENDING;
    }

    public function isRejected(): bool
    {
        return $this->approval_status === self::APPROVAL_REJECTED;
    }

    public function canBePublished(): bool
    {
        return $this->isApproved() && $this->publish_status !== 'published';
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('publish_status', 'published')
                    ->where('is_public', true)
                    ->where('approval_status', self::APPROVAL_APPROVED); // 🎯 TAMBAH INI
    }

    // 🎯 TAMBAH APPROVAL SCOPES
    public function scopePendingApproval($query)
    {
        return $query->where('approval_status', self::APPROVAL_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('approval_status', self::APPROVAL_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('approval_status', self::APPROVAL_REJECTED);
    }

    public function scopeByTopic($query, $topic)
    {
        return $query->where('topic', $topic);
    }

    public function scopeBySector($query, $sector)
    {
        return $query->where('sector', $sector);
    }

    public function scopeByClassification($query, $classification)
    {
        return $query->where('classification', $classification);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('tags', 'like', "%{$search}%");
        });
    }

    // Methods
    public function getPaginatedData($perPage = 10, $page = 1)
    {
        $data = $this->data ?? [];
        $total = count($data);
        $offset = ($page - 1) * $perPage;
        
        return [
            'data' => array_slice($data, $offset, $perPage),
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ];
    }

    public function incrementView()
    {
        $this->increment('view_count');
    }

    public function incrementDownload()
    {
        $this->increment('download_count');
    }

    public function publish()
    {
        $this->update([
            'publish_status' => 'published',
            'published_at' => now()
        ]);
    }

    public function unpublish()
    {
        $this->update([
            'publish_status' => 'draft',
            'published_at' => null
        ]);
    }

    public function archive()
    {
        $this->update([
            'publish_status' => 'archived'
        ]);
    }

    // 🎯 TAMBAH APPROVAL METHODS
    public function approve($approvedById, $notes = null)
    {
        return $this->update([
            'approval_status' => self::APPROVAL_APPROVED,
            'publish_status' => 'published',
            'approved_by' => $approvedById,
            'approved_at' => now(),
            'approval_notes' => $notes,
            'published_at' => now(),
            'rejection_reason' => null
        ]);
    }

    public function reject($rejectedById, $reason, $notes = null)
    {
        return $this->update([
            'approval_status' => self::APPROVAL_REJECTED,
            'approved_by' => $rejectedById,
            'approved_at' => now(),
            'rejection_reason' => $reason,
            'approval_notes' => $notes,
            'publish_status' => 'draft'
        ]);
    }

    public function resubmit()
    {
        return $this->update([
            'approval_status' => self::APPROVAL_PENDING,
            'approved_by' => null,
            'approved_at' => null,
            'rejection_reason' => null,
            'approval_notes' => null,
            'publish_status' => 'draft'
        ]);
    }

    public function getPreviewData($limit = 5)
    {
        $data = $this->data ?? [];
        return array_slice($data, 0, $limit);
    }

    public function getColumnStats()
    {
        $data = $this->data ?? [];
        $headers = $this->headers ?? [];
        $stats = [];

        foreach ($headers as $index => $header) {
            $columnData = array_column($data, $index);
            $nonEmptyData = array_filter($columnData, function($value) {
                return $value !== null && $value !== '';
            });
            
            $stats[$header] = [
                'total_values' => count($columnData),
                'non_empty_values' => count($nonEmptyData),
                'empty_percentage' => count($columnData) > 0 ? 
                    round((count($columnData) - count($nonEmptyData)) / count($columnData) * 100, 2) : 0,
                'sample_values' => array_slice(array_unique($nonEmptyData), 0, 3)
            ];
        }

        return $stats;
    }

    public function addProcessingLog($step, $message, $data = null)
    {
        $logs = $this->processing_log ?? [];
        $logs[] = [
            'step' => $step,
            'message' => $message,
            'data' => $data,
            'timestamp' => now()->toISOString()
        ];
        
        $this->update(['processing_log' => $logs]);
    }

    // Static methods
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

    public static function getClassifications()
    {
        return [
            'publik' => 'Publik',
            'internal' => 'Internal',
            'terbatas' => 'Terbatas',
            'rahasia' => 'Rahasia'
        ];
    }

    public static function getSectors()
    {
        return [
            'pemerintahan' => 'Pemerintahan',
            'swasta' => 'Swasta',
            'akademik' => 'Akademik',
            'non-profit' => 'Non-Profit'
        ];
    }

    public static function getLicenses()
    {
        return [
            'cc-by' => 'Creative Commons BY',
            'cc-by-sa' => 'Creative Commons BY-SA',
            'cc-by-nc' => 'Creative Commons BY-NC',
            'public-domain' => 'Public Domain',
            'proprietary' => 'Proprietary',
            'other' => 'Other'
        ];
    }

    // 🎯 TAMBAH GET APPROVAL STATUSES
    public static function getApprovalStatuses()
    {
        return [
            self::APPROVAL_PENDING => 'Pending',
            self::APPROVAL_APPROVED => 'Approved',
            self::APPROVAL_REJECTED => 'Rejected'
        ];
    }
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

}