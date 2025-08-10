<?php
/**
 * Migration Files untuk Open Data Ternate
 * Berdasarkan struktur Open Data Jabar
 * Database: PostgreSQL dengan Laravel
 */

// =============================================
// 1. CREATE CATEGORIES TABLE
// File: 2025_01_01_000001_create_categories_table.php
// =============================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->string('icon', 50)->nullable();
            $table->string('color', 20)->nullable();
            $table->uuid('parent_id')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index('slug');
            $table->index('parent_id');
            $table->index('is_active');
            
            // Foreign key
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
};

// =============================================
// 2. CREATE ORGANIZATIONS TABLE
// File: 2025_01_01_000002_create_organizations_table.php
// =============================================

return new class extends Migration
{
    public function up()
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('acronym', 20)->nullable();
            $table->text('description')->nullable();
            $table->enum('type', ['dinas', 'badan', 'sekretariat', 'dprd', 'inspektorat', 'rsud', 'uptd', 'others'])->default('dinas');
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('website')->nullable();
            $table->string('head_name', 100)->nullable(); // Nama Kepala/Pimpinan
            $table->string('head_title', 100)->nullable(); // Jabatan Kepala
            $table->string('logo_url')->nullable();
            $table->date('establishment_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable(); // Data tambahan yang fleksibel
            $table->timestamps();

            // Indexes
            $table->index('slug');
            $table->index('type');
            $table->index('is_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('organizations');
    }
};

// =============================================
// 3. CREATE TAGS TABLE
// File: 2025_01_01_000003_create_tags_table.php
// =============================================

return new class extends Migration
{
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100)->unique();
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->string('color', 20)->nullable();
            $table->integer('usage_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index('slug');
            $table->index('usage_count');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tags');
    }
};

// =============================================
// 4. CREATE USERS TABLE (Extended)
// File: 2025_01_01_000004_create_users_table.php
// =============================================

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone', 20)->nullable();
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable();
            $table->uuid('organization_id')->nullable();
            $table->enum('role', ['super_admin', 'admin', 'editor', 'contributor', 'viewer'])->default('viewer');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->json('permissions')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();

            // Indexes
            $table->index('email');
            $table->index('organization_id');
            $table->index('role');
            $table->index('status');
            
            // Foreign key
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};

// =============================================
// 5. CREATE DATASETS TABLE
// File: 2025_01_01_000005_create_datasets_table.php
// =============================================

return new class extends Migration
{
    public function up()
    {
        Schema::create('datasets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title', 500);
            $table->string('slug', 500)->unique();
            $table->text('description')->nullable();
            $table->text('summary')->nullable(); // Ringkasan singkat
            
            // References
            $table->uuid('category_id');
            $table->uuid('organization_id');
            $table->uuid('created_by');
            $table->uuid('updated_by')->nullable();
            
            // Status & Visibility
            $table->enum('status', ['draft', 'published', 'archived', 'under_review'])->default('draft');
            $table->enum('visibility', ['public', 'restricted', 'private'])->default('public');
            $table->enum('license', ['cc-by', 'cc-by-sa', 'cc-by-nc', 'cc-by-nd', 'cc0', 'odbl', 'custom'])->default('cc-by');
            
            // Metadata
            $table->string('source')->nullable(); // Sumber data
            $table->string('methodology')->nullable(); // Metodologi pengumpulan
            $table->date('data_period_start')->nullable(); // Periode data mulai
            $table->date('data_period_end')->nullable(); // Periode data berakhir
            $table->string('update_frequency')->nullable(); // Frekuensi update (daily, weekly, monthly, yearly)
            $table->timestamp('last_updated_data')->nullable(); // Terakhir data diupdate
            
            // Geographic scope
            $table->string('geographic_scope')->default('ternate'); // ternate, malut, national
            $table->json('geographic_coverage')->nullable(); // Detail wilayah cakupan
            
            // Quality & Completeness
            $table->decimal('completeness_percentage', 5, 2)->default(0); // Persentase kelengkapan data
            $table->integer('quality_score')->default(0); // Skor kualitas (0-100)
            $table->text('quality_notes')->nullable(); // Catatan kualitas
            
            // Statistics
            $table->bigInteger('view_count')->default(0);
            $table->bigInteger('download_count')->default(0);
            $table->decimal('rating_average', 3, 2)->default(0);
            $table->integer('rating_count')->default(0);
            
            // SEO & Search
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('keywords')->nullable(); // Kata kunci untuk pencarian
            
            // Publishing
            $table->timestamp('published_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            
            // Additional metadata
            $table->json('custom_fields')->nullable(); // Field tambahan yang fleksibel
            $table->json('api_config')->nullable(); // Konfigurasi API jika ada
            
            $table->timestamps();

            // Indexes
            $table->index('slug');
            $table->index('category_id');
            $table->index('organization_id');
            $table->index('created_by');
            $table->index('status');
            $table->index('visibility');
            $table->index('published_at');
            $table->index(['status', 'visibility']);
            
            // Full text search index (PostgreSQL)
            $table->index(['title', 'description'], null, 'gin');
            
            // Foreign keys
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('organization_id')->references('id')->on('organizations');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('datasets');
    }
};

// =============================================
// 6. CREATE DATASET_RESOURCES TABLE
// File: 2025_01_01_000006_create_dataset_resources_table.php
// =============================================

return new class extends Migration
{
    public function up()
    {
        Schema::create('dataset_resources', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('dataset_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['csv', 'json', 'xml', 'xlsx', 'pdf', 'api', 'shapefile', 'geojson', 'others'])->default('csv');
            $table->enum('format', ['csv', 'json', 'xml', 'xlsx', 'xls', 'pdf', 'api', 'shp', 'geojson', 'kml', 'others']);
            
            // File information
            $table->string('file_path')->nullable(); // Path file di storage
            $table->string('file_name')->nullable(); // Nama file asli
            $table->string('file_url')->nullable(); // URL eksternal jika ada
            $table->bigInteger('file_size')->nullable(); // Size dalam bytes
            $table->string('mime_type')->nullable();
            $table->string('encoding', 20)->default('UTF-8');
            
            // API specific
            $table->string('api_endpoint')->nullable();
            $table->string('api_method', 10)->default('GET');
            $table->json('api_parameters')->nullable();
            $table->text('api_documentation')->nullable();
            
            // Data structure
            $table->json('schema')->nullable(); // Schema kolom untuk data terstruktur
            $table->integer('rows_count')->nullable(); // Jumlah baris data
            $table->integer('columns_count')->nullable(); // Jumlah kolom
            $table->json('sample_data')->nullable(); // Sample data untuk preview
            
            // Status & Quality
            $table->enum('status', ['active', 'inactive', 'processing', 'error'])->default('active');
            $table->text('validation_errors')->nullable();
            $table->timestamp('last_validated_at')->nullable();
            $table->timestamp('last_modified')->nullable(); // Kapan file terakhir dimodifikasi
            
            // Access & Download
            $table->bigInteger('download_count')->default(0);
            $table->boolean('is_downloadable')->default(true);
            $table->boolean('requires_auth')->default(false);
            
            // Versioning
            $table->string('version', 20)->default('1.0');
            $table->uuid('previous_version_id')->nullable();
            
            $table->timestamps();

            // Indexes
            $table->index('dataset_id');
            $table->index('type');
            $table->index('format');
            $table->index('status');
            $table->index('is_downloadable');
            
            // Foreign keys
            $table->foreign('dataset_id')->references('id')->on('datasets')->onDelete('cascade');
            $table->foreign('previous_version_id')->references('id')->on('dataset_resources')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('dataset_resources');
    }
};

// =============================================
// 7. CREATE DATASET_TAGS TABLE (Many-to-Many)
// File: 2025_01_01_000007_create_dataset_tags_table.php
// =============================================

return new class extends Migration
{
    public function up()
    {
        Schema::create('dataset_tags', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('dataset_id');
            $table->uuid('tag_id');
            $table->timestamps();

            // Indexes
            $table->unique(['dataset_id', 'tag_id']);
            $table->index('dataset_id');
            $table->index('tag_id');
            
            // Foreign keys
            $table->foreign('dataset_id')->references('id')->on('datasets')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('dataset_tags');
    }
};

// =============================================
// 8. CREATE VISUALIZATIONS TABLE
// File: 2025_01_01_000008_create_visualizations_table.php
// =============================================

return new class extends Migration
{
    public function up()
    {
        Schema::create('visualizations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->uuid('dataset_id')->nullable(); // Bisa tidak terkait dengan dataset tertentu
            $table->uuid('created_by');
            
            // Visualization config
            $table->enum('type', ['chart', 'map', 'infographic', 'dashboard', 'table'])->default('chart');
            $table->enum('chart_type', ['bar', 'line', 'pie', 'scatter', 'area', 'heatmap', 'others'])->nullable();
            $table->json('config'); // Konfigurasi chart/visualization
            $table->json('data_source'); // Sumber data dan query
            
            // Display
            $table->string('thumbnail')->nullable();
            $table->text('embed_code')->nullable(); // Kode embed untuk external use
            $table->boolean('is_public')->default(true);
            $table->boolean('is_featured')->default(false);
            
            // Interaction
            $table->bigInteger('view_count')->default(0);
            $table->bigInteger('embed_count')->default(0);
            
            $table->timestamps();

            // Indexes
            $table->index('slug');
            $table->index('dataset_id');
            $table->index('created_by');
            $table->index('type');
            $table->index('is_public');
            $table->index('is_featured');
            
            // Foreign keys
            $table->foreign('dataset_id')->references('id')->on('datasets')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('visualizations');
    }
};

// =============================================
// 9. CREATE ARTICLES TABLE
// File: 2025_01_01_000009_create_articles_table.php
// =============================================

return new class extends Migration
{
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('featured_image')->nullable();
            $table->uuid('author_id');
            $table->uuid('category_id')->nullable();
            
            // Publishing
            $table->enum('status', ['draft', 'published', 'scheduled', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            
            // Engagement
            $table->bigInteger('view_count')->default(0);
            $table->bigInteger('share_count')->default(0);
            $table->decimal('reading_time', 5, 2)->nullable(); // Estimasi waktu baca dalam menit
            
            // Related data
            $table->json('related_datasets')->nullable(); // ID dataset terkait
            $table->json('related_visualizations')->nullable(); // ID visualisasi terkait
            
            $table->timestamps();

            // Indexes
            $table->index('slug');
            $table->index('author_id');
            $table->index('category_id');
            $table->index('status');
            $table->index('published_at');
            
            // Foreign keys
            $table->foreign('author_id')->references('id')->on('users');
            $table->foreign('category_id')->references('id')->on('categories');
        });
    }

    public function down()
    {
        Schema::dropIfExists('articles');
    }
};

// =============================================
// 10. CREATE INFOGRAPHICS TABLE
// File: 2025_01_01_000010_create_infographics_table.php
// =============================================

return new class extends Migration
{
    public function up()
    {
        Schema::create('infographics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image_url'); // URL gambar infografik
            $table->string('thumbnail_url')->nullable(); // URL thumbnail
            $table->uuid('created_by');
            $table->uuid('category_id')->nullable();
            
            // File info
            $table->string('file_path');
            $table->bigInteger('file_size')->nullable();
            $table->string('dimensions')->nullable(); // Format: "width x height"
            $table->string('file_format', 10)->default('png'); // png, jpg, svg, pdf
            
            // Publishing
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            
            // Engagement
            $table->bigInteger('view_count')->default(0);
            $table->bigInteger('download_count')->default(0);
            $table->bigInteger('share_count')->default(0);
            
            // Related data
            $table->json('related_datasets')->nullable(); // ID dataset sumber
            $table->json('tags')->nullable(); // Tags untuk infografik
            
            $table->timestamps();

            // Indexes
            $table->index('slug');
            $table->index('created_by');
            $table->index('category_id');
            $table->index('status');
            $table->index('published_at');
            
            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('category_id')->references('id')->on('categories');
        });
    }

    public function down()
    {
        Schema::dropIfExists('infographics');
    }
};

// =============================================
// 11. CREATE DATASET_REQUESTS TABLE
// File: 2025_01_01_000011_create_dataset_requests_table.php
// =============================================

return new class extends Migration
{
    public function up()
    {
        Schema::create('dataset_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description');
            $table->string('requester_name');
            $table->string('requester_email');
            $table->string('requester_phone', 20)->nullable();
            $table->string('organization')->nullable();
            $table->text('purpose'); // Tujuan penggunaan data
            
            // Request details
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->date('needed_by')->nullable(); // Kapan data dibutuhkan
            $table->json('preferred_formats')->nullable(); // Format yang diinginkan
            $table->text('additional_notes')->nullable();
            
            // Assignment
            $table->uuid('assigned_to')->nullable(); // Admin/PIC yang menangani
            $table->uuid('organization_id')->nullable(); // OPD yang bertanggung jawab
            
            // Status tracking
            $table->enum('status', ['pending', 'in_progress', 'completed', 'rejected', 'cancelled'])->default('pending');
            $table->text('admin_notes')->nullable(); // Catatan dari admin
            $table->text('rejection_reason')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            // Result
            $table->uuid('resulting_dataset_id')->nullable(); // Dataset yang dihasilkan dari request ini
            
            $table->timestamps();

            // Indexes
            $table->index('requester_email');
            $table->index('assigned_to');
            $table->index('organization_id');
            $table->index('status');
            $table->index('priority');
            $table->index('needed_by');
            
            // Foreign keys
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('set null');
            $table->foreign('resulting_dataset_id')->references('id')->on('datasets')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('dataset_requests');
    }
};

// =============================================
// 12. CREATE ACTIVITY_LOGS TABLE
// File: 2025_01_01_000012_create_activity_logs_table.php
// =============================================

return new class extends Migration
{
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->string('action'); // create, update, delete, view, download, etc.
            $table->string('model_type'); // Model yang diaksi (Dataset, Organization, etc.)
            $table->uuid('model_id'); // ID dari model
            $table->json('old_values')->nullable(); // Nilai sebelum perubahan
            $table->json('new_values')->nullable(); // Nilai setelah perubahan
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable(); // Data tambahan
            $table->timestamp('created_at');

            // Indexes
            $table->index('user_id');
            $table->index('action');
            $table->index(['model_type', 'model_id']);
            $table->index('created_at');
            
            // Foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('activity_logs');
    }
};

// =============================================
// 13. CREATE SYSTEM_SETTINGS TABLE
// File: 2025_01_01_000013_create_system_settings_table.php
// =============================================

return new class extends Migration
{
    public function up()
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('key')->unique(); // Kunci setting
            $table->text('value')->nullable(); // Nilai setting
            $table->string('type', 50)->default('string'); // string, integer, boolean, json, array
            $table->string('group', 100)->default('general'); // Grup setting
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false); // Apakah bisa diakses publik via API
            $table->timestamps();

            // Indexes
            $table->index('key');
            $table->index('group');
            $table->index('is_public');
        });
    }

    public function down()
    {
        Schema::dropIfExists('system_settings');
    }
};

// =============================================
// SEEDER DATA AWAL
// File: database/seeders/OpenDataTernateSeeder.php
// =============================================

/**
 * Seeder untuk data awal Open Data Ternate
 * Jalankan dengan: php artisan db:seed --class=OpenDataTernateSeeder
 */