<?php

use App\Http\Controllers\Api\V1\DatasetApiController;
use App\Http\Controllers\Api\V1\GlobalSearchApiController;
use App\Http\Controllers\Api\V1\InfografisApiController;
use App\Http\Controllers\Api\V1\MapsetApiController;
use App\Http\Controllers\Api\V1\VisualisasiApiController;
use App\Http\Controllers\Api\V1\OrganizationApiController;
use Illuminate\Support\Facades\Route;


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
        Route::get('/', [DatasetApiController::class, 'index'])->name('index');
       
        
        // Individual dataset
        Route::get('/{dataset:slug}', [DatasetApiController::class, 'show'])->name('show');
           
    });
    Route::get('/total-data', [DatasetApiController::class, 'getPublicStats'])->name('total-data');
    /*
    |--------------------------------------------------------------------------
    | Infografis Routes (Read Only)
    |--------------------------------------------------------------------------
    */
    Route::prefix('infografis')->name('infografis.')->group(function () {
        Route::get('/', [InfografisApiController::class, 'index'])->name('index');
        Route::get('/{infografis}', [InfografisApiController::class, 'show'])->name('show');
        
        // Additional endpoints for better API functionality
        Route::get('/search/suggestions', [InfografisApiController::class, 'suggestions'])->name('suggestions');
        Route::get('/search/query', [InfografisApiController::class, 'search'])->name('search');
        Route::get('/topic/{topic}', [InfografisApiController::class, 'byTopic'])->name('by-topic');
        Route::get('/meta/topics', [InfografisApiController::class, 'topics'])->name('topics');
        Route::get('/meta/tags', [InfografisApiController::class, 'tags'])->name('tags');
        Route::get('/meta/stats', [InfografisApiController::class, 'stats'])->name('stats');
    });

    /*
    |--------------------------------------------------------------------------
    | Mapset Routes (Read Only)
    |--------------------------------------------------------------------------
    */
    Route::prefix('mapsets')->name('mapsets.')->group(function () {
        // List and search
        Route::get('/', [MapsetApiController::class, 'index'])->name('index');
        Route::get('/{mapset:slug}', [MapsetApiController::class, 'show'])->name('show');
        
    });

    /*
    |--------------------------------------------------------------------------
    | Visualisasi Routes (Read Only)
    |--------------------------------------------------------------------------
    */
    Route::prefix('visualisasi')->name('visualisasi.')->group(function () {
        // List and search
        Route::get('/', [VisualisasiApiController::class, 'index'])->name('index');
       
        // Individual visualisasi
        Route::get('/{visualisasi:slug}', [VisualisasiApiController::class, 'show'])->name('show');
    });

    /*
    |--------------------------------------------------------------------------
    | Organization Routes (Read Only)
    |--------------------------------------------------------------------------
    */
    Route::prefix('organizations')->name('organizations.')->group(function () {
        // List and search
        Route::get('/', [OrganizationApiController::class, 'index'])->name('index');     
        
        // Individual organization
        Route::get('/{organization:slug}', [OrganizationApiController::class, 'show'])->name('show');
        
    });

    /*|--------------------------------------------------------------------------
    | Global Search Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('search')->name('search.')->group(function () {
        // Main global search endpoint
        Route::get('/global', [GlobalSearchApiController::class, 'globalSearch'])->name('global');
        
        // Search utilities
        Route::get('/autocomplete', [GlobalSearchApiController::class, 'autocomplete'])->name('autocomplete');
        
        // Legacy search endpoints (keep for backward compatibility)
        Route::get('/suggestions', [DatasetApiController::class, 'getSearchSuggestions'])->name('suggestions');
    });

  
});
