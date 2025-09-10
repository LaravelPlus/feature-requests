<?php

use Illuminate\Support\Facades\Route;
use LaravelPlus\FeatureRequests\Http\Controllers\FeatureRequestController;
use LaravelPlus\FeatureRequests\Http\Controllers\VoteController;
use LaravelPlus\FeatureRequests\Http\Controllers\CategoryController;
use LaravelPlus\FeatureRequests\Http\Controllers\CommentController;

/*
|--------------------------------------------------------------------------
| Feature Requests API Routes
|--------------------------------------------------------------------------
|
| Here are the API routes for the feature requests package. These routes
| are loaded by the RouteServiceProvider within a group which is assigned
| the "api" middleware group.
|
*/

Route::prefix('feature-requests')->name('feature-requests.')->group(function () {
    
    // Feature Requests
    Route::get('/', [FeatureRequestController::class, 'index'])->name('index');
    Route::post('/', [FeatureRequestController::class, 'store'])->name('store');
    Route::get('/statistics', [FeatureRequestController::class, 'statistics'])->name('statistics');
    Route::get('/needing-attention', [FeatureRequestController::class, 'needingAttention'])->name('needing-attention');
    
    Route::get('/{slug}', [FeatureRequestController::class, 'show'])->name('show');
    Route::put('/{slug}', [FeatureRequestController::class, 'update'])->name('update');
    Route::delete('/{slug}', [FeatureRequestController::class, 'destroy'])->name('destroy');
    
    // Feature Request Actions
    Route::patch('/{slug}/status', [FeatureRequestController::class, 'updateStatus'])->name('update-status');
    Route::patch('/{slug}/assign', [FeatureRequestController::class, 'assign'])->name('assign');
    Route::patch('/{slug}/toggle-featured', [FeatureRequestController::class, 'toggleFeatured'])->name('toggle-featured');
    
    // Votes
    Route::prefix('votes')->name('votes.')->group(function () {
        Route::post('/', [VoteController::class, 'vote'])->name('vote');
        Route::delete('/', [VoteController::class, 'removeVote'])->name('remove');
        Route::get('/statistics', [VoteController::class, 'getStatistics'])->name('statistics');
        Route::get('/most-voted', [VoteController::class, 'getMostVoted'])->name('most-voted');
        Route::get('/user-statistics', [VoteController::class, 'getUserStatistics'])->name('user-statistics');
        
        Route::get('/feature-request/{feature_request_id}', [VoteController::class, 'getVotes'])->name('by-feature-request');
        Route::get('/feature-request/{feature_request_id}/up', [VoteController::class, 'getUpVotes'])->name('up-by-feature-request');
        Route::get('/feature-request/{feature_request_id}/down', [VoteController::class, 'getDownVotes'])->name('down-by-feature-request');
        Route::get('/feature-request/{feature_request_id}/has-voted', [VoteController::class, 'hasVoted'])->name('has-voted');
        
        Route::get('/user/{user_id?}', [VoteController::class, 'getUserVotes'])->name('by-user');
        Route::get('/user/{user_id?}/up', [VoteController::class, 'getUserUpVotes'])->name('up-by-user');
        Route::get('/user/{user_id?}/down', [VoteController::class, 'getUserDownVotes'])->name('down-by-user');
    });
    
    // Comments
    Route::prefix('comments')->name('comments.')->group(function () {
        Route::get('/', [CommentController::class, 'index'])->name('index');
        Route::post('/', [CommentController::class, 'store'])->name('store');
        Route::get('/statistics', [CommentController::class, 'statistics'])->name('statistics');
        Route::get('/recent', [CommentController::class, 'getRecent'])->name('recent');
        Route::get('/search', [CommentController::class, 'search'])->name('search');
        
        Route::get('/feature-request/{feature_request_id}', [CommentController::class, 'getByFeatureRequest'])->name('by-feature-request');
        Route::get('/feature-request/{feature_request_id}/top-level', [CommentController::class, 'getTopLevelByFeatureRequest'])->name('top-level-by-feature-request');
        Route::get('/parent/{parent_id}/replies', [CommentController::class, 'getReplies'])->name('replies');
        Route::get('/user/{user_id?}', [CommentController::class, 'getByUser'])->name('by-user');
        Route::get('/approved', [CommentController::class, 'getApproved'])->name('approved');
        Route::get('/pending', [CommentController::class, 'getPending'])->name('pending');
        Route::get('/pinned', [CommentController::class, 'getPinned'])->name('pinned');
        
        Route::get('/{id}', [CommentController::class, 'show'])->name('show');
        Route::put('/{id}', [CommentController::class, 'update'])->name('update');
        Route::delete('/{id}', [CommentController::class, 'destroy'])->name('destroy');
        
        Route::patch('/{id}/approve', [CommentController::class, 'approve'])->name('approve');
        Route::patch('/{id}/pin', [CommentController::class, 'pin'])->name('pin');
        Route::patch('/{id}/unpin', [CommentController::class, 'unpin'])->name('unpin');
    });
    
    // Categories
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/all', [CategoryController::class, 'all'])->name('all');
        Route::get('/active', [CategoryController::class, 'active'])->name('active');
        Route::get('/with-counts', [CategoryController::class, 'withCounts'])->name('with-counts');
        Route::get('/active-with-counts', [CategoryController::class, 'activeWithCounts'])->name('active-with-counts');
        Route::get('/default', [CategoryController::class, 'default'])->name('default');
        Route::get('/statistics', [CategoryController::class, 'statistics'])->name('statistics');
        Route::post('/create-defaults', [CategoryController::class, 'createDefaults'])->name('create-defaults');
        
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::post('/reorder', [CategoryController::class, 'reorder'])->name('reorder');
        
        Route::get('/{slug}', [CategoryController::class, 'show'])->name('show');
        Route::put('/{slug}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{slug}', [CategoryController::class, 'destroy'])->name('destroy');
        Route::patch('/{slug}/toggle-active', [CategoryController::class, 'toggleActive'])->name('toggle-active');
    });
});
