<?php

use App\Http\Controllers\DatasetController;
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
        Route::get('/import', [DatasetController::class, 'create'])->name('create');
        Route::post('/import', [DatasetController::class, 'store'])->name('store');
        Route::get('/compare', [DatasetController::class, 'compare'])->name('compare');
        Route::get('/bulk-actions', [DatasetController::class, 'bulkActions'])->name('bulk');
        Route::post('/bulk-action', [DatasetController::class, 'executeBulkAction'])->name('bulk.execute');
        Route::get('/{id}', [DatasetController::class, 'show'])->name('show');
        Route::get('/{id}/statistics', [DatasetController::class, 'statistics'])->name('statistics');
        Route::get('/{id}/search', [DatasetController::class, 'search'])->name('search');
        Route::delete('/{id}', [DatasetController::class, 'destroy'])->name('destroy');
    });
    Route::get('dataset/{id}/download', [DatasetController::class, 'download'])->name('dataset.download');
    
    // API Routes
    Route::prefix('api')->group(function () {
        Route::get('/dataset/{id}', [DatasetController::class, 'api'])->name('dataset.api');
    });
});

require __DIR__.'/auth.php';
