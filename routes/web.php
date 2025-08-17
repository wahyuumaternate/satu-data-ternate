<?php

use App\Http\Controllers\DatasetApprovalController;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\MapsetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VisualisasiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Dashboard Route
Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    
    /*
    |--------------------------------------------------------------------------
    | Profile Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });
    
    /*
    |--------------------------------------------------------------------------
    | Dataset Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('dataset')->name('dataset.')->group(function () {
        // Main CRUD operations
        Route::get('/', [DatasetController::class, 'index'])->name('index');
        Route::get('/create', [DatasetController::class, 'create'])->name('create');
        Route::post('/import', [DatasetController::class, 'store'])->name('store');
        
        // Dataset utilities
        Route::get('/compare', [DatasetController::class, 'compare'])->name('compare');
        Route::get('/history', [DatasetController::class, 'history'])->name('history');
        Route::get('/bulk-actions', [DatasetController::class, 'bulkActions'])->name('bulk');
        Route::post('/bulk-action', [DatasetController::class, 'executeBulkAction'])->name('bulk.execute');
        
        // Individual dataset operations
        Route::get('/{slug}', [DatasetController::class, 'show'])->name('show');
        Route::get('/{id}/statistics', [DatasetController::class, 'statistics'])->name('statistics');
        Route::get('/{id}/search', [DatasetController::class, 'search'])->name('search');
        Route::get('/{id}/download', [DatasetController::class, 'download'])->name('download');
        Route::delete('/{slug}', [DatasetController::class, 'destroy'])->name('destroy');
    });
    
    /*
    |--------------------------------------------------------------------------
    | Visualization Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('visualisasi')->name('visualisasi.')->group(function () {
        // Template download
        Route::get('/download-template', [VisualisasiController::class, 'downloadTemplate'])->name('download-template');
        
        // Main CRUD operations
        Route::get('/', [VisualisasiController::class, 'index'])->name('index');
        Route::get('/create', [VisualisasiController::class, 'create'])->name('create');
        Route::post('/', [VisualisasiController::class, 'store'])->name('store');
        Route::get('/{visualisasi}', [VisualisasiController::class, 'show'])->name('show');
        Route::get('/{visualisasi}/edit', [VisualisasiController::class, 'edit'])->name('edit');
        Route::put('/{visualisasi}', [VisualisasiController::class, 'update'])->name('update');
        Route::patch('/{visualisasi}', [VisualisasiController::class, 'update'])->name('update');
        Route::delete('/{visualisasi}', [VisualisasiController::class, 'destroy'])->name('destroy');
        
        // Export operations
        Route::get('/{visualisasi}/export-csv', [VisualisasiController::class, 'exportCsv'])->name('export-csv');
        Route::get('/{visualisasi}/export-json', [VisualisasiController::class, 'exportJson'])->name('export-json');
    });
    
    /*
    |--------------------------------------------------------------------------
    | Mapset Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('mapset')->name('mapset.')->group(function () {
        // Main CRUD operations
        Route::get('/', [MapsetController::class, 'index'])->name('index');
        Route::get('/create', [MapsetController::class, 'create'])->name('create');
        Route::post('/', [MapsetController::class, 'store'])->name('store');
        Route::get('/{uuid}', [MapsetController::class, 'show'])->name('show');
        Route::get('/{uuid}/edit', [MapsetController::class, 'edit'])->name('edit');
        Route::put('/{uuid}', [MapsetController::class, 'update'])->name('update');
        Route::delete('/{uuid}', [MapsetController::class, 'destroy'])->name('destroy');
        
        // Download operations
        Route::get('/{uuid}/download/geojson', [MapsetController::class, 'downloadGeojson'])->name('download.geojson');
    });
    
    // Map view (separate from CRUD)
    Route::get('/mapset-map', [MapsetController::class, 'map'])->name('mapset.map');
    
    /*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('api')->name('api.')->group(function () {
        // Dataset API
        Route::get('/dataset/{id}', [DatasetController::class, 'api'])->name('dataset');
        
        // Mapset API
        Route::get('/mapset/geojson', [MapsetController::class, 'geojson'])->name('mapset.geojson');
        Route::get('/mapset/{uuid}/data', [MapsetController::class, 'getMapsetData'])->name('mapset.data');
        Route::get('/mapset/statistics', [MapsetController::class, 'getStatistics'])->name('mapset.statistics');
        Route::get('/mapset/dbf-columns', [MapsetController::class, 'getDbfColumns'])->name('mapset.dbf-columns');
        Route::get('/mapset/dbf-values/{column}', [MapsetController::class, 'getDbfColumnValues'])->name('mapset.dbf-values');
        
        // Debug endpoints (development only)
        Route::post('/mapset/debug/shapefile', [MapsetController::class, 'debugShapefile'])->name('mapset.debug.shapefile');
        Route::post('/mapset/debug/kmz', [MapsetController::class, 'debugKmz'])->name('mapset.debug.kmz');
    });
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    
    /*
    |--------------------------------------------------------------------------
    | Dataset Approval Management
    |--------------------------------------------------------------------------
    */
    Route::prefix('dataset-approval')->name('dataset-approval.')->group(function () {
        // Main approval views
        Route::get('/', [DatasetApprovalController::class, 'index'])->name('index');
        Route::get('/approved', [DatasetApprovalController::class, 'approved'])->name('approved');
        Route::get('/rejected', [DatasetApprovalController::class, 'rejected'])->name('rejected');
        Route::get('/{dataset}', [DatasetApprovalController::class, 'show'])->name('show');
        
        // Approval actions
        Route::post('/{dataset}/approve', [DatasetApprovalController::class, 'approve'])->name('approve');
        Route::post('/{dataset}/reject', [DatasetApprovalController::class, 'reject'])->name('reject');
        Route::post('/{dataset}/resubmit', [DatasetApprovalController::class, 'resubmit'])->name('resubmit');
        
        // Bulk operations
        Route::post('/bulk-approve', [DatasetApprovalController::class, 'bulkApprove'])->name('bulk-approve');
    });
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';