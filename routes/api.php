<?php

use App\Http\Controllers\Api\V1\DatasetController;
use App\Http\Controllers\Api\V1\InfografisController;
use App\Http\Controllers\Api\V1\MapsetController;
use App\Http\Controllers\Api\V1\VisualisasiController;
use App\Http\Controllers\Api\V1\OrganizationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Basic user route (optional)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| API Version 1 - Public Access
|--------------------------------------------------------------------------
*/
Route::prefix('v1')->name('api.v1.')->group(function () {
    
    /*
    |--------------------------------------------------------------------------
    | Dataset Routes (Read Only)
    |--------------------------------------------------------------------------
    */
    Route::prefix('datasets')->name('datasets.')->group(function () {
        // List and search
        Route::get('/', [DatasetController::class, 'index'])->name('index');
       
        
        // Individual dataset
        Route::get('/{dataset:slug}', [DatasetController::class, 'show'])->name('show');
           
    });
    Route::get('/total-data', [DatasetController::class, 'getPublicStats'])->name('total-data');
    /*
    |--------------------------------------------------------------------------
    | Infografis Routes (Read Only)
    |--------------------------------------------------------------------------
    */
    Route::prefix('infografis')->name('infografis.')->group(function () {
        // List and search
        Route::get('/', [InfografisController::class, 'index'])->name('index');
        Route::get('/search', [InfografisController::class, 'search'])->name('search');
        Route::get('/topics', [InfografisController::class, 'getTopics'])->name('topics');
        Route::get('/popular', [InfografisController::class, 'getPopular'])->name('popular');
        Route::get('/recent', [InfografisController::class, 'getRecent'])->name('recent');
        Route::get('/featured', [InfografisController::class, 'getFeatured'])->name('featured');
        
        // Individual infografis
        Route::get('/{infografis}', [InfografisController::class, 'show'])->name('show');
        Route::get('/{infografis}/metadata', [InfografisController::class, 'getMetadata'])->name('metadata');
        Route::get('/{infografis}/download', [InfografisController::class, 'download'])->name('download');
        Route::get('/{infografis}/info', [InfografisController::class, 'getInfo'])->name('info');
        
        // Filter routes
        Route::get('/by-topic/{topic}', [InfografisController::class, 'getByTopic'])->name('by-topic');
        Route::get('/by-organization/{organization}', [InfografisController::class, 'getByOrganization'])->name('by-organization');
        Route::get('/by-year/{year}', [InfografisController::class, 'getByYear'])->name('by-year');
        
        // Export catalog
        Route::get('/catalog/export', [InfografisController::class, 'exportCatalog'])->name('export-catalog');
    });

    /*
    |--------------------------------------------------------------------------
    | Mapset Routes (Read Only)
    |--------------------------------------------------------------------------
    */
    Route::prefix('mapsets')->name('mapsets.')->group(function () {
        // List and search
        Route::get('/', [MapsetController::class, 'index'])->name('index');
        Route::get('/{mapset:slug}', [MapsetController::class, 'show'])->name('show');
        
    });

    /*
    |--------------------------------------------------------------------------
    | Visualisasi Routes (Read Only)
    |--------------------------------------------------------------------------
    */
    Route::prefix('visualisasi')->name('visualisasi.')->group(function () {
        // List and search
        Route::get('/', [VisualisasiController::class, 'index'])->name('index');
        Route::get('/search', [VisualisasiController::class, 'search'])->name('search');
        Route::get('/types', [VisualisasiController::class, 'getTypes'])->name('types');
        Route::get('/popular', [VisualisasiController::class, 'getPopular'])->name('popular');
        Route::get('/recent', [VisualisasiController::class, 'getRecent'])->name('recent');
        Route::get('/featured', [VisualisasiController::class, 'getFeatured'])->name('featured');
        
        // Individual visualisasi
        Route::get('/{visualisasi}', [VisualisasiController::class, 'show'])->name('show');
        Route::get('/{visualisasi}/data', [VisualisasiController::class, 'getData'])->name('data');
        Route::get('/{visualisasi}/config', [VisualisasiController::class, 'getConfig'])->name('config');
        Route::get('/{visualisasi}/export-csv', [VisualisasiController::class, 'exportCsv'])->name('export-csv');
        Route::get('/{visualisasi}/export-json', [VisualisasiController::class, 'exportJson'])->name('export-json');
        
        // Filter routes
        Route::get('/by-type/{type}', [VisualisasiController::class, 'getByType'])->name('by-type');
        Route::get('/by-organization/{organization}', [VisualisasiController::class, 'getByOrganization'])->name('by-organization');
        Route::get('/by-topic/{topic}', [VisualisasiController::class, 'getByTopic'])->name('by-topic');
        
        // Export catalog
        Route::get('/catalog/export', [VisualisasiController::class, 'exportCatalog'])->name('export-catalog');
    });

    /*
    |--------------------------------------------------------------------------
    | Organization Routes (Read Only)
    |--------------------------------------------------------------------------
    */
    Route::prefix('organizations')->name('organizations.')->group(function () {
        // List and search
        Route::get('/', [OrganizationController::class, 'index'])->name('index');
        Route::get('/search', [OrganizationController::class, 'search'])->name('search');
        Route::get('/active', [OrganizationController::class, 'getActive'])->name('active');
        Route::get('/statistics', [OrganizationController::class, 'getStatistics'])->name('statistics');
        Route::get('/types', [OrganizationController::class, 'getTypes'])->name('types');
        
        // Individual organization
        Route::get('/{organization}', [OrganizationController::class, 'show'])->name('show');
        Route::get('/{organization}/datasets', [OrganizationController::class, 'getDatasets'])->name('datasets');
        Route::get('/{organization}/infografis', [OrganizationController::class, 'getInfografis'])->name('infografis');
        Route::get('/{organization}/mapsets', [OrganizationController::class, 'getMapsets'])->name('mapsets');
        Route::get('/{organization}/visualisasi', [OrganizationController::class, 'getVisualisasi'])->name('visualisasi');
        Route::get('/{organization}/stats', [OrganizationController::class, 'getOrganizationStats'])->name('stats');
        
        // Filter routes
        Route::get('/by-type/{type}', [OrganizationController::class, 'getByType'])->name('by-type');
        Route::get('/by-region/{region}', [OrganizationController::class, 'getByRegion'])->name('by-region');
        
        // Top organizations
        Route::get('/top/contributors', [OrganizationController::class, 'getTopContributors'])->name('top-contributors');
        Route::get('/top/downloaders', [OrganizationController::class, 'getTopDownloaders'])->name('top-downloaders');
        
        // Export catalog
        Route::get('/catalog/export', [OrganizationController::class, 'exportCatalog'])->name('export-catalog');
    });

    /*
    |--------------------------------------------------------------------------
    | Global Search Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('search')->name('search.')->group(function () {
        Route::get('/global', [DatasetController::class, 'globalSearch'])->name('global');
        Route::get('/autocomplete', [DatasetController::class, 'autocomplete'])->name('autocomplete');
        Route::get('/suggestions', [DatasetController::class, 'getSearchSuggestions'])->name('suggestions');
        Route::get('/popular-terms', [DatasetController::class, 'getPopularSearchTerms'])->name('popular-terms');
    });

    /*
    |--------------------------------------------------------------------------
    | Statistics and Analytics Routes (Public)
    |--------------------------------------------------------------------------
    */
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/overview', [DatasetController::class, 'getAnalyticsOverview'])->name('overview');
        Route::get('/popular-content', [DatasetController::class, 'getPopularContent'])->name('popular-content');
        Route::get('/download-stats', [DatasetController::class, 'getDownloadStats'])->name('download-stats');
        Route::get('/monthly-stats', [DatasetController::class, 'getMonthlyStats'])->name('monthly-stats');
        Route::get('/content-summary', [DatasetController::class, 'getContentSummary'])->name('content-summary');
    });

    /*
    |--------------------------------------------------------------------------
    | Categories and Filters
    |--------------------------------------------------------------------------
    */
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/topics', [DatasetController::class, 'getAllTopics'])->name('topics');
        Route::get('/organizations', [DatasetController::class, 'getOrganizations'])->name('organizations');
        Route::get('/formats', [DatasetController::class, 'getDataFormats'])->name('formats');
        Route::get('/years', [DatasetController::class, 'getAvailableYears'])->name('years');
        Route::get('/chart-types', [VisualisasiController::class, 'getChartTypes'])->name('chart-types');
        Route::get('/regions', [MapsetController::class, 'getRegions'])->name('regions');
    });

    /*
    |--------------------------------------------------------------------------
    | Featured and Highlights
    |--------------------------------------------------------------------------
    */
    Route::prefix('featured')->name('featured.')->group(function () {
        Route::get('/datasets', [DatasetController::class, 'getFeatured'])->name('datasets');
        Route::get('/infografis', [InfografisController::class, 'getFeatured'])->name('infografis');
        Route::get('/mapsets', [MapsetController::class, 'getFeatured'])->name('mapsets');
        Route::get('/visualisasi', [VisualisasiController::class, 'getFeatured'])->name('visualisasi');
        Route::get('/all', [DatasetController::class, 'getAllFeatured'])->name('all');
    });

    /*
    |--------------------------------------------------------------------------
    | Health Check and System Info
    |--------------------------------------------------------------------------
    */
    Route::get('/health', function () {
        return response()->json([
            'status' => 'healthy',
            'timestamp' => now(),
            'version' => '1.0.0',
            'services' => [
                'datasets' => 'operational',
                'infografis' => 'operational',
                'mapsets' => 'operational',
                'visualisasi' => 'operational',
                'organizations' => 'operational'
            ]
        ]);
    })->name('health');

    Route::get('/system-info', function () {
        return response()->json([
            'api_version' => 'v1',
            'laravel_version' => app()->version(),
            'server_time' => now(),
            'timezone' => config('app.timezone'),
            'available_endpoints' => [
                'datasets' => '/api/v1/datasets',
                'infografis' => '/api/v1/infografis',
                'mapsets' => '/api/v1/mapsets',
                'visualisasi' => '/api/v1/visualisasi',
                'organizations' => '/api/v1/organizations'
            ],
            'access' => 'Public API - No authentication required'
        ]);
    })->name('system-info');
});

/*
|--------------------------------------------------------------------------
| API Documentation Route
|--------------------------------------------------------------------------
*/
Route::get('/v1/docs', function () {
    return response()->json([
        'api_documentation' => [
            'title' => 'Data Portal API',
            'version' => 'v1',
            'description' => 'Public API for accessing datasets, infografis, mapsets, visualisasi, and organizations',
            'base_url' => url('/api/v1'),
            'authentication' => 'None - Public API',
            'endpoints' => [
                'datasets' => [
                    'base_url' => '/api/v1/datasets',
                    'methods' => [
                        'GET /' => 'List all datasets',
                        'GET /search' => 'Search datasets',
                        'GET /{slug}' => 'Get dataset details',
                        'GET /{slug}/data' => 'Get dataset data',
                        'GET /{slug}/download' => 'Download dataset file',
                        'GET /popular' => 'Get popular datasets',
                        'GET /recent' => 'Get recent datasets',
                        'GET /topics' => 'Get available topics',
                    ]
                ],
                'infografis' => [
                    'base_url' => '/api/v1/infografis',
                    'methods' => [
                        'GET /' => 'List all infografis',
                        'GET /search' => 'Search infografis',
                        'GET /{id}' => 'Get infografis details',
                        'GET /{id}/download' => 'Download infografis',
                        'GET /popular' => 'Get popular infografis',
                    ]
                ]
            ],
            'rate_limits' => [
                'requests_per_minute' => 60,
            ],
            'response_format' => [
                'success' => [
                    'success' => true,
                    'message' => 'Operation successful',
                    'data' => '{ ... }',
                    'timestamp' => now()->toISOString()
                ],
                'error' => [
                    'success' => false,
                    'message' => 'Error description',
                    'timestamp' => now()->toISOString()
                ]
            ]
        ]
    ], 200, [], JSON_PRETTY_PRINT);
})->name('api.v1.docs');