<?php

use App\Http\Controllers\Api\V1\DatasetController;
use App\Http\Controllers\Api\V1\InfografisApiController;
use App\Http\Controllers\Api\V1\InfografisController;
use App\Http\Controllers\Api\V1\MapsetController;
use App\Http\Controllers\Api\V1\VisualisasiController;
use App\Http\Controllers\Api\V1\OrganizationController;
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
       
        // Individual visualisasi
        Route::get('/{visualisasi:slug}', [VisualisasiController::class, 'show'])->name('show');
    });

    /*
    |--------------------------------------------------------------------------
    | Organization Routes (Read Only)
    |--------------------------------------------------------------------------
    */
    Route::prefix('organizations')->name('organizations.')->group(function () {
        // List and search
        Route::get('/', [OrganizationController::class, 'index'])->name('index');     
        
        // Individual organization
        Route::get('/{organization:slug}', [OrganizationController::class, 'show'])->name('show');
        
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


  
});
