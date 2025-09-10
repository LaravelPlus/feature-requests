<?php

use Illuminate\Support\Facades\Route;
use LaravelPlus\FeatureRequests\Http\Controllers\FeatureRequestController;
use LaravelPlus\FeatureRequests\Http\Controllers\CategoryController;

/*
|--------------------------------------------------------------------------
| Feature Requests Web Routes
|--------------------------------------------------------------------------
|
| Here are the web routes for the feature requests package. These routes
| are loaded by the RouteServiceProvider within a group which is assigned
| the "web" middleware group.
|
*/

Route::prefix('feature-requests')->name('feature-requests.')->middleware(['web'])->group(function () {
    
    // Feature Requests
    Route::get('/', [FeatureRequestController::class, 'index'])->name('index');
    Route::get('/create', [FeatureRequestController::class, 'create'])->name('create');
    Route::post('/', [FeatureRequestController::class, 'store'])->name('store');
    
    Route::get('/{slug}', [FeatureRequestController::class, 'show'])->name('show');
    Route::get('/{slug}/edit', [FeatureRequestController::class, 'edit'])->name('edit');
    Route::put('/{slug}', [FeatureRequestController::class, 'update'])->name('update');
    Route::delete('/{slug}', [FeatureRequestController::class, 'destroy'])->name('destroy');
    
    // Categories
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        
        Route::get('/{slug}', [CategoryController::class, 'show'])->name('show');
        Route::get('/{slug}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('/{slug}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{slug}', [CategoryController::class, 'destroy'])->name('destroy');
    });
});
