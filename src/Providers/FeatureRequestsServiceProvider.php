<?php

namespace LaravelPlus\FeatureRequests\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use LaravelPlus\FeatureRequests\Repositories\FeatureRequestRepository;
use LaravelPlus\FeatureRequests\Repositories\VoteRepository;
use LaravelPlus\FeatureRequests\Repositories\CategoryRepository;
use LaravelPlus\FeatureRequests\Repositories\CommentRepository;
use LaravelPlus\FeatureRequests\Services\FeatureRequestService;
use LaravelPlus\FeatureRequests\Services\VoteService;
use LaravelPlus\FeatureRequests\Services\CategoryService;
use LaravelPlus\FeatureRequests\Services\CommentService;
use LaravelPlus\FeatureRequests\Models\FeatureRequest;
use LaravelPlus\FeatureRequests\Models\Vote;
use LaravelPlus\FeatureRequests\Models\Category;
use LaravelPlus\FeatureRequests\Models\Comment;

class FeatureRequestsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register configuration
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/feature-requests.php',
            'feature-requests'
        );

        // Register repositories
        $this->app->bind(FeatureRequestRepository::class, function ($app) {
            return new FeatureRequestRepository($app->make(FeatureRequest::class));
        });

        $this->app->bind(VoteRepository::class, function ($app) {
            return new VoteRepository($app->make(Vote::class));
        });

        $this->app->bind(CategoryRepository::class, function ($app) {
            return new CategoryRepository($app->make(Category::class));
        });

        $this->app->bind(CommentRepository::class, function ($app) {
            return new CommentRepository($app->make(Comment::class));
        });

        // Register services
        $this->app->bind(FeatureRequestService::class, function ($app) {
            return new FeatureRequestService(
                $app->make(FeatureRequestRepository::class),
                $app->make(VoteRepository::class),
                $app->make(CommentRepository::class)
            );
        });

        $this->app->bind(VoteService::class, function ($app) {
            return new VoteService(
                $app->make(VoteRepository::class),
                $app->make(FeatureRequestRepository::class)
            );
        });

        $this->app->bind(CategoryService::class, function ($app) {
            return new CategoryService($app->make(CategoryRepository::class));
        });

        $this->app->bind(CommentService::class, function ($app) {
            return new CommentService(
                $app->make(CommentRepository::class),
                $app->make(FeatureRequestRepository::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish configuration
        $this->publishes([
            __DIR__ . '/../../config/feature-requests.php' => config_path('feature-requests.php'),
        ], 'feature-requests-config');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../Database/Migrations' => database_path('migrations'),
        ], 'feature-requests-migrations');

        // Publish views
        $this->publishes([
            __DIR__ . '/../../resources/views' => resource_path('views/vendor/feature-requests'),
        ], 'feature-requests-views');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'feature-requests');

        // Load routes
        $this->loadRoutes();

        // Load translations
        $this->loadTranslationsFrom(__DIR__ . '/../../resources/lang', 'feature-requests');

        // Publish translations
        $this->publishes([
            __DIR__ . '/../../resources/lang' => $this->app->langPath('vendor/feature-requests'),
        ], 'feature-requests-translations');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                // Add commands here if needed
            ]);
        }
    }

    /**
     * Load routes.
     */
    protected function loadRoutes(): void
    {
        // Load API routes
        Route::prefix('api')
            ->middleware('api')
            ->group(__DIR__ . '/../../routes/api.php');

        // Load customer web routes
        Route::middleware('web')
            ->group(__DIR__ . '/../../routes/web.php');

        // Load admin routes
        Route::middleware('web')
            ->group(__DIR__ . '/../../routes/admin.php');
    }
}
