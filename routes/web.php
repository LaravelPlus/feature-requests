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

Route::prefix('feature-requests')->name('feature-requests.')->middleware(['web', 'auth'])->group(function () {
    
    // Feature Requests (Customer View) - All require authentication
    Route::get('/', [FeatureRequestController::class, 'publicIndex'])->name('index');
    
    // Create Feature Request - Must come before /{slug}
    Route::get('/create', [FeatureRequestController::class, 'create'])->name('create');
    Route::post('/', [FeatureRequestController::class, 'store'])->name('store');
    
    Route::get('/{slug}', [FeatureRequestController::class, 'publicShow'])->name('show');
    
    // Voting
    Route::post('/{slug}/vote', [VoteController::class, 'store'])->name('vote');
    Route::delete('/{slug}/vote', [VoteController::class, 'destroy'])->name('unvote');
    
    // Comments
    Route::post('/{slug}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    
    // Categories (Read-only)
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'publicIndex'])->name('index');
        Route::get('/{slug}', [CategoryController::class, 'publicShow'])->name('show');
    });
});
