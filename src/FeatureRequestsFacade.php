<?php

namespace LaravelPlus\FeatureRequests;

use Illuminate\Support\Facades\Facade;
use LaravelPlus\FeatureRequests\Services\FeatureRequestService;
use LaravelPlus\FeatureRequests\Services\VoteService;
use LaravelPlus\FeatureRequests\Services\CategoryService;
use LaravelPlus\FeatureRequests\Services\CommentService;

/**
 * @method static \LaravelPlus\FeatureRequests\Services\FeatureRequestService featureRequests()
 * @method static \LaravelPlus\FeatureRequests\Services\VoteService votes()
 * @method static \LaravelPlus\FeatureRequests\Services\CategoryService categories()
 * @method static \LaravelPlus\FeatureRequests\Services\CommentService comments()
 */
class FeatureRequestsFacade extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'feature-requests';
    }

    /**
     * Get the feature request service.
     */
    public static function featureRequests(): FeatureRequestService
    {
        return app(FeatureRequestService::class);
    }

    /**
     * Get the vote service.
     */
    public static function votes(): VoteService
    {
        return app(VoteService::class);
    }

    /**
     * Get the category service.
     */
    public static function categories(): CategoryService
    {
        return app(CategoryService::class);
    }

    /**
     * Get the comment service.
     */
    public static function comments(): CommentService
    {
        return app(CommentService::class);
    }
}
