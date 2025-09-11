<?php

use Illuminate\Support\Facades\Route;
use LaravelPlus\FeatureRequests\Http\Controllers\FeatureRequestController;
use LaravelPlus\FeatureRequests\Http\Controllers\CategoryController;
use LaravelPlus\FeatureRequests\Http\Controllers\VoteController;
use LaravelPlus\FeatureRequests\Http\Controllers\CommentController;

/*
|--------------------------------------------------------------------------
| Feature Requests Customer Routes
|--------------------------------------------------------------------------
|
| Here are the customer-facing routes for the feature requests package.
| These routes allow customers to view, vote, and comment on feature requests.
|
*/

Route::prefix('feature-requests')->name('feature-requests.')->middleware(['auth'])->group(function () {
    
    // Feature Requests (Customer View) - All require authentication
    Route::get('/', [FeatureRequestController::class, 'publicIndex'])->name('index');
    
    // Roadmap - Must come before /{slug}
    Route::get('/roadmap', [FeatureRequestController::class, 'roadmap'])->name('roadmap');
    
    // Changelog - Must come before /{slug}
    Route::get('/changelog', [FeatureRequestController::class, 'changelog'])->name('changelog');
    
    // Create Feature Request - Must come before /{slug}
    Route::get('/create', [FeatureRequestController::class, 'create'])->name('create');
    Route::post('/', [FeatureRequestController::class, 'store'])->name('store');
    
    Route::get('/{identifier}', [FeatureRequestController::class, 'publicShow'])->name('show')->where('identifier', '[a-zA-Z0-9\-_]+');
    
    // Voting
    Route::post('/{identifier}/vote', [VoteController::class, 'store'])->name('vote')->where('identifier', '[a-zA-Z0-9\-_]+');
    Route::delete('/{identifier}/vote', [VoteController::class, 'destroy'])->name('unvote')->where('identifier', '[a-zA-Z0-9\-_]+');
    
    // Comments
    Route::post('/{identifier}/comments', [CommentController::class, 'store'])->name('comments.store')->where('identifier', '[a-zA-Z0-9\-_]+');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    
    // Categories (Read-only)
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'publicIndex'])->name('index');
        Route::get('/{slug}', [CategoryController::class, 'publicShow'])->name('show');
    });
});
