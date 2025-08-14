<?php

use App\Http\Controllers\DatasetApprovalController;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\DatasetRequestController;
use App\Http\Controllers\MapsetController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    
    Route::prefix('dataset')->name('dataset.')->group(function () {
        Route::get('/', [DatasetController::class, 'index'])->name('index');
        Route::get('/create', [DatasetController::class, 'create'])->name('create');
        Route::post('/import', [DatasetController::class, 'store'])->name('store');
        Route::get('/compare', [DatasetController::class, 'compare'])->name('compare');
        Route::get('/bulk-actions', [DatasetController::class, 'bulkActions'])->name('bulk');
        Route::post('/bulk-action', [DatasetController::class, 'executeBulkAction'])->name('bulk.execute');
        Route::get('/{slug}', [DatasetController::class, 'show'])->name('show');
        Route::get('/{id}/statistics', [DatasetController::class, 'statistics'])->name('statistics');
        Route::get('/{id}/search', [DatasetController::class, 'search'])->name('search');
        Route::delete('/{id}', [DatasetController::class, 'destroy'])->name('destroy');
         Route::get('/history', [DatasetController::class, 'history'])->name('history');
    });
    Route::get('dataset/{id}/download', [DatasetController::class, 'download'])->name('dataset.download');
    
    // API Routes
    Route::prefix('api')->group(function () {
        Route::get('/dataset/{id}', [DatasetController::class, 'api'])->name('dataset.api');
    });

    Route::resource('mapset', MapsetController::class);
    Route::get('mapset/{mapset}/download-geojson', [MapsetController::class, 'downloadGeojson'])
    ->name('mapset.download-geojson');
});

// 🎯 DATASET ROUTES (for all users)
Route::middleware('auth')->group(function () {
    Route::resource('dataset', DatasetController::class);
    Route::get('dataset/{dataset}/download', [DatasetController::class, 'download'])->name('dataset.download');
    Route::get('dataset/{dataset}/api', [DatasetController::class, 'api'])->name('dataset.api');
});

// // 🎯 PUBLIC DATASET ROUTES (no auth required)
// Route::get('browse-datasets', [DatasetController::class, 'publicIndex'])->name('dataset.public');
// Route::get('view-dataset/{dataset:slug}', [DatasetController::class, 'publicShow'])->name('dataset.public.show');

// 🎯 ADMIN APPROVAL ROUTES (tanpa middleware admin untuk sementara)
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::prefix('dataset-approval')->name('dataset-approval.')->group(function () {
        Route::get('/', [DatasetApprovalController::class, 'index'])->name('index');
        Route::get('/approved', [DatasetApprovalController::class, 'approved'])->name('approved');
        Route::get('/rejected', [DatasetApprovalController::class, 'rejected'])->name('rejected');
        Route::get('/{dataset}', [DatasetApprovalController::class, 'show'])->name('show');
        
        // Approval Actions
        Route::post('/{dataset}/approve', [DatasetApprovalController::class, 'approve'])->name('approve');
        Route::post('/{dataset}/reject', [DatasetApprovalController::class, 'reject'])->name('reject');
        Route::post('/{dataset}/resubmit', [DatasetApprovalController::class, 'resubmit'])->name('resubmit');
        
        // Bulk Actions
        Route::post('/bulk-approve', [DatasetApprovalController::class, 'bulkApprove'])->name('bulk-approve');
    });
});

require __DIR__.'/auth.php';
