<?php

/**
 * CARA INSTALASI & SETUP
 * 
 * 1. Jalankan migration:
 *    php artisan migrate
 * 
 * 2. Jalankan seeder:
 *    php artisan db:seed --class=OpenDataTernateSeeder
 * 
 * 3. Install UUID untuk PostgreSQL (jika belum):
 *    Tambah di AppServiceProvider boot method:
 *    Schema::defaultStringLength(191);
 */

// =============================================
// MODEL: Category
// File: app/Models/Category.php
// =============================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    use HasUuids;

    protected $fillable = [
        'name', 'slug', 'description', 'icon', 'color', 
        'parent_id', 'sort_order', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Relations
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function datasets(): HasMany
    {
        return $this->hasMany(Dataset::class);
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function infographics(): HasMany
    {
        return $this->hasMany(Infographic::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeParent($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}

// =============================================
// MODEL: Organization
// File: app/Models/Organization.php
// =============================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    use HasUuids;

    protected $fillable = [
        'name', 'slug', 'acronym', 'description', 'type', 'address',
        'phone', 'email', 'website', 'head_name', 'head_title',
        'logo_url', 'establishment_date', 'is_active', 'metadata'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'establishment_date' => 'date',
        'metadata' => 'array',
    ];

    // Relations
    public function datasets(): HasMany
    {
        return $this->hasMany(Dataset::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function datasetRequests(): HasMany
    {
        return $this->hasMany(DatasetRequest::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Accessors
    public function getDisplayNameAttribute()
    {
        return $this->acronym ? "{$this->name} ({$this->acronym})" : $this->name;
    }
}

// =============================================
// MODEL: Dataset
// File: app/Models/Dataset.php
// =============================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Dataset extends Model
{
    use HasUuids;

    protected $fillable = [
        'title', 'slug', 'description', 'summary', 'category_id', 
        'organization_id', 'created_by', 'updated_by', 'status', 
        'visibility', 'license', 'source', 'methodology',
        'data_period_start', 'data_period_end', 'update_frequency',
        'last_updated_data', 'geographic_scope', 'geographic_coverage',
        'completeness_percentage', 'quality_score', 'quality_notes',
        'view_count', 'download_count', 'rating_average', 'rating_count',
        'meta_title', 'meta_description', 'keywords', 'published_at',
        'archived_at', 'custom_fields', 'api_config'
    ];

    protected $casts = [
        'data_period_start' => 'date',
        'data_period_end' => 'date',
        'last_updated_data' => 'datetime',
        'geographic_coverage' => 'array',
        'completeness_percentage' => 'decimal:2',
        'quality_score' => 'integer',
        'view_count' => 'integer',
        'download_count' => 'integer',
        'rating_average' => 'decimal:2',
        'rating_count' => 'integer',
        'published_at' => 'datetime',
        'archived_at' => 'datetime',
        'custom_fields' => 'array',
        'api_config' => 'array',
    ];

    // Relations
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function resources(): HasMany
    {
        return $this->hasMany(DatasetResource::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'dataset_tags');
    }

    public function visualizations(): HasMany
    {
        return $this->hasMany(Visualization::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->where('visibility', 'public')
                    ->whereNotNull('published_at');
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByOrganization($query, $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    public function scopePopular($query)
    {
        return $query->orderBy('view_count', 'desc')
                    ->orderBy('download_count', 'desc');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('published_at', 'desc');
    }

    // Accessors
    public function getIsPublishedAttribute()
    {
        return $this->status === 'published' && 
               $this->visibility === 'public' && 
               !is_null($this->published_at);
    }

    public function getFormattedSizeAttribute()
    {
        $totalSize = $this->resources->sum('file_size');
        return $this->formatBytes($totalSize);
    }

    // Mutators
    public function setKeywordsAttribute($value)
    {
        $this->attributes['keywords'] = is_array($value) ? implode(', ', $value) : $value;
    }

    // Helper methods
    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        return round($size, $precision) . ' ' . $units[$i];
    }

    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    public function incrementDownloadCount()
    {
        $this->increment('download_count');
    }
}

// =============================================
// MODEL: DatasetResource
// File: app/Models/DatasetResource.php
// =============================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DatasetResource extends Model
{
    use HasUuids;

    protected $fillable = [
        'dataset_id', 'name', 'description', 'type', 'format',
        'file_path', 'file_name', 'file_url', 'file_size', 'mime_type', 'encoding',
        'api_endpoint', 'api_method', 'api_parameters', 'api_documentation',
        'schema', 'rows_count', 'columns_count', 'sample_data',
        'status', 'validation_errors', 'last_validated_at', 'last_modified',
        'download_count', 'is_downloadable', 'requires_auth',
        'version', 'previous_version_id'
    ];

    protected $casts = [
        'api_parameters' => 'array',
        'schema' => 'array',
        'sample_data' => 'array',
        'rows_count' => 'integer',
        'columns_count' => 'integer',
        'file_size' => 'integer',
        'download_count' => 'integer',
        'is_downloadable' => 'boolean',
        'requires_auth' => 'boolean',
        'last_validated_at' => 'datetime',
        'last_modified' => 'datetime',
    ];

    // Relations
    public function dataset(): BelongsTo
    {
        return $this->belongsTo(Dataset::class);
    }

    public function previousVersion(): BelongsTo
    {
        return $this->belongsTo(DatasetResource::class, 'previous_version_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDownloadable($query)
    {
        return $query->where('is_downloadable', true);
    }

    // Accessors
    public function getFormattedSizeAttribute()
    {
        return $this->formatBytes($this->file_size);
    }

    public function getIsApiResourceAttribute()
    {
        return $this->type === 'api' && !empty($this->api_endpoint);
    }

    // Helper methods
    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        return round($size, $precision) . ' ' . $units[$i];
    }

    public function incrementDownloadCount()
    {
        $this->increment('download_count');
        $this->dataset->incrementDownloadCount();
    }
}

// =============================================
// MODEL: Tag
// File: app/Models/Tag.php
// =============================================

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasUuids;

    protected $fillable = [
        'name', 'slug', 'description', 'color', 'usage_count', 'is_active'
    ];

    protected $casts = [
        'usage_count' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relations
    public function datasets(): BelongsToMany
    {
        return $this->belongsToMany(Dataset::class, 'dataset_tags');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePopular($query)
    {
        return $query->orderBy('usage_count', 'desc');
    }

    // Methods
    public function incrementUsage()
    {
        $this->increment('usage_count');
    }
}

// =============================================
// MODEL: User (Extended)
// File: app/Models/User.php
// =============================================

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'bio', 'avatar',
        'organization_id', 'role', 'status', 'permissions', 'last_login_at'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'permissions' => 'array',
    ];

    // Relations
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function datasets(): HasMany
    {
        return $this->hasMany(Dataset::class, 'created_by');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'author_id');
    }

    public function visualizations(): HasMany
    {
        return $this->hasMany(Visualization::class, 'created_by');
    }

    public function infographics(): HasMany
    {
        return $this->hasMany(Infographic::class, 'created_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    // Methods
    public function hasPermission($permission)
    {
        if ($this->role === 'super_admin') {
            return true;
        }

        return in_array($permission, $this->permissions ?? []);
    }

    public function isAdmin()
    {
        return in_array($this->role, ['super_admin', 'admin']);
    }

    public function canManageDataset(Dataset $dataset = null)
    {
        if ($this->role === 'super_admin') {
            return true;
        }

        if ($this->role === 'admin') {
            return true;
        }

        if ($dataset && $this->role === 'editor') {
            return $dataset->organization_id === $this->organization_id;
        }

        return false;
    }
}

// =============================================
// ARTISAN COMMAND: Setup Open Data
// File: app/Console/Commands/SetupOpenDataCommand.php
// =============================================

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SetupOpenDataCommand extends Command
{
    protected $signature = 'opendata:setup {--fresh : Fresh installation (drop existing tables)}';
    protected $description = 'Setup Open Data Ternate database and initial data';

    public function handle()
    {
        $this->info('🚀 Setting up Open Data Ternate...');

        if ($this->option('fresh')) {
            $this->warn('⚠️  Fresh installation will drop all existing tables!');
            if (!$this->confirm('Are you sure you want to continue?')) {
                $this->info('Operation cancelled.');
                return;
            }

            $this->info('📦 Running fresh migration...');
            Artisan::call('migrate:fresh');
            $this->info(Artisan::output());
        } else {
            $this->info('📦 Running migrations...');
            Artisan::call('migrate');
            $this->info(Artisan::output());
        }

        $this->info('🌱 Seeding initial data...');
        Artisan::call('db:seed', ['--class' => 'OpenDataTernateSeeder']);
        $this->info(Artisan::output());

        // Show statistics
        $this->showStatistics();

        $this->info('✅ Open Data Ternate setup completed successfully!');
        $this->info('');
        $this->info('Default login credentials:');
        $this->info('Email: admin@opendata.ternatekota.go.id');
        $this->info('Password: admin123');
        $this->info('');
        $this->warn('⚠️  Please change the default password after first login!');
    }

    private function showStatistics()
    {
        $this->info('');
        $this->info('📊 Database Statistics:');
        
        $stats = [
            'Categories' => DB::table('categories')->count(),
            'Organizations' => DB::table('organizations')->count(),
            'Tags' => DB::table('tags')->count(),
            'Users' => DB::table('users')->count(),
            'System Settings' => DB::table('system_settings')->count(),
        ];

        foreach ($stats as $label => $count) {
            $this->info("   {$label}: {$count}");
        }
        $this->info('');
    }
}

// =============================================
// SERVICE: Dataset Service
// File: app/Services/DatasetService.php
// =============================================

namespace App\Services;

use App\Models\Dataset;
use App\Models\DatasetResource;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DatasetService
{
    public function createDataset(array $data, array $resources = [])
    {
        $dataset = Dataset::create([
            ...$data,
            'slug' => $this->generateUniqueSlug($data['title']),
            'created_by' => auth()->id(),
        ]);

        if (!empty($resources)) {
            $this->attachResources($dataset, $resources);
        }

        return $dataset;
    }

    public function updateDataset(Dataset $dataset, array $data, array $resources = [])
    {
        $dataset->update([
            ...$data,
            'updated_by' => auth()->id(),
        ]);

        if (!empty($resources)) {
            $this->attachResources($dataset, $resources);
        }

        return $dataset;
    }

    public function attachResources(Dataset $dataset, array $resources)
    {
        foreach ($resources as $resourceData) {
            if (isset($resourceData['file']) && $resourceData['file'] instanceof UploadedFile) {
                $this->handleFileUpload($dataset, $resourceData);
            } else {
                $this->createApiResource($dataset, $resourceData);
            }
        }
    }

    private function handleFileUpload(Dataset $dataset, array $resourceData)
    {
        $file = $resourceData['file'];
        $path = $file->store('datasets/' . $dataset->id, 'public');
        
        DatasetResource::create([
            'dataset_id' => $dataset->id,
            'name' => $resourceData['name'] ?? $file->getClientOriginalName(),
            'description' => $resourceData['description'] ?? null,
            'type' => $this->determineFileType($file),
            'format' => $file->getClientOriginalExtension(),
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'is_downloadable' => true,
        ]);
    }

    private function createApiResource(Dataset $dataset, array $resourceData)
    {
        DatasetResource::create([
            'dataset_id' => $dataset->id,
            'name' => $resourceData['name'],
            'description' => $resourceData['description'] ?? null,
            'type' => 'api',
            'format' => $resourceData['format'] ?? 'json',
            'api_endpoint' => $resourceData['api_endpoint'],
            'api_method' => $resourceData['api_method'] ?? 'GET',
            'api_parameters' => $resourceData['api_parameters'] ?? null,
            'api_documentation' => $resourceData['api_documentation'] ?? null,
        ]);
    }

    private function determineFileType(UploadedFile $file)
    {
        $extension = strtolower($file->getClientOriginalExtension());
        
        $typeMap = [
            'csv' => 'csv',
            'json' => 'json',
            'xml' => 'xml',
            'xlsx' => 'xlsx',
            'xls' => 'xlsx',
            'pdf' => 'pdf',
            'shp' => 'shapefile',
            'geojson' => 'geojson',
        ];

        return $typeMap[$extension] ?? 'others';
    }

    private function generateUniqueSlug(string $title)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (Dataset::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function publishDataset(Dataset $dataset)
    {
        $dataset->update([
            'status' => 'published',
            'published_at' => now(),
            'visibility' => 'public',
        ]);

        return $dataset;
    }

    public function archiveDataset(Dataset $dataset)
    {
        $dataset->update([
            'status' => 'archived',
            'archived_at' => now(),
        ]);

        return $dataset;
    }
}

// =============================================
// API RESOURCE: Dataset Resource
// File: app/Http/Resources/DatasetResource.php
// =============================================

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DatasetResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'summary' => $this->summary,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'organization' => new OrganizationResource($this->whenLoaded('organization')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'resources' => DatasetResourceResource::collection($this->whenLoaded('resources')),
            'status' => $this->status,
            'visibility' => $this->visibility,
            'license' => $this->license,
            'source' => $this->source,
            'data_period' => [
                'start' => $this->data_period_start?->format('Y-m-d'),
                'end' => $this->data_period_end?->format('Y-m-d'),
            ],
            'update_frequency' => $this->update_frequency,
            'last_updated_data' => $this->last_updated_data?->format('Y-m-d H:i:s'),
            'geographic_scope' => $this->geographic_scope,
            'geographic_coverage' => $this->geographic_coverage,
            'quality' => [
                'completeness_percentage' => $this->completeness_percentage,
                'quality_score' => $this->quality_score,
                'quality_notes' => $this->quality_notes,
            ],
            'statistics' => [
                'view_count' => $this->view_count,
                'download_count' => $this->download_count,
                'rating_average' => $this->rating_average,
                'rating_count' => $this->rating_count,
            ],
            'meta' => [
                'title' => $this->meta_title,
                'description' => $this->meta_description,
                'keywords' => $this->keywords,
            ],
            'timestamps' => [
                'created_at' => $this->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
                'published_at' => $this->published_at?->format('Y-m-d H:i:s'),
            ],
        ];
    }
}