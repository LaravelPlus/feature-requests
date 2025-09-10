<?php

use Illuminate\Support\Facades\Route;
use LaravelPlus\FeatureRequests\Http\Controllers\FeatureRequestController;
use LaravelPlus\FeatureRequests\Http\Controllers\CategoryController;
use LaravelPlus\FeatureRequests\Http\Controllers\VoteController;
use LaravelPlus\FeatureRequests\Http\Controllers\CommentController;

/*
|--------------------------------------------------------------------------
| Feature Requests Admin Routes
|--------------------------------------------------------------------------
|
| Here are the admin routes for the feature requests package. These routes
| are for administrators to manage feature requests, categories, and users.
|
*/

Route::prefix('admin/feature-requests')->name('admin.feature-requests.')->middleware(['web', 'auth'])->group(function () {
    
    // Feature Requests Admin
    Route::get('/', [FeatureRequestController::class, 'adminIndex'])->name('index');
    Route::get('/create', [FeatureRequestController::class, 'create'])->name('create');
    Route::post('/', [FeatureRequestController::class, 'store'])->name('store');
    
    Route::get('/{slug}', [FeatureRequestController::class, 'adminShow'])->name('show');
    Route::get('/{slug}/edit', [FeatureRequestController::class, 'edit'])->name('edit');
    Route::put('/{slug}', [FeatureRequestController::class, 'update'])->name('update');
    Route::delete('/{slug}', [FeatureRequestController::class, 'destroy'])->name('destroy');
    
    // Admin Actions
    Route::patch('/{slug}/status', [FeatureRequestController::class, 'updateStatus'])->name('update-status');
    Route::patch('/{slug}/assign', [FeatureRequestController::class, 'assign'])->name('assign');
    Route::patch('/{slug}/toggle-featured', [FeatureRequestController::class, 'toggleFeatured'])->name('toggle-featured');
    
    // Voting (Admin can manage votes)
    Route::post('/{slug}/vote', [VoteController::class, 'store'])->name('vote');
    Route::delete('/{slug}/vote', [VoteController::class, 'destroy'])->name('unvote');
    
    // Comments (Admin can manage comments)
    Route::post('/{slug}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::patch('/comments/{comment}/approve', [CommentController::class, 'approve'])->name('comments.approve');
    Route::patch('/comments/{comment}/pin', [CommentController::class, 'pin'])->name('comments.pin');
    
    // Categories Admin
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'adminIndex'])->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        
        Route::get('/{slug}', [CategoryController::class, 'adminShow'])->name('show');
        Route::get('/{slug}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('/{slug}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{slug}', [CategoryController::class, 'destroy'])->name('destroy');
        Route::patch('/{slug}/toggle-active', [CategoryController::class, 'toggleActive'])->name('toggle-active');
    });
    
    // Statistics and Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/statistics', [FeatureRequestController::class, 'statistics'])->name('statistics');
        Route::get('/needing-attention', [FeatureRequestController::class, 'needingAttention'])->name('needing-attention');
        Route::get('/vote-statistics', [VoteController::class, 'getStatistics'])->name('vote-statistics');
        Route::get('/comment-statistics', [CommentController::class, 'statistics'])->name('comment-statistics');
    });
});
