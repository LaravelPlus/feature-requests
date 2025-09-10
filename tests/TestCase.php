<?php

namespace LaravelPlus\FeatureRequests\Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Load package service provider
        $this->app->register(\LaravelPlus\FeatureRequests\Providers\FeatureRequestsServiceProvider::class);
    }

    protected function createUser(array $attributes = []): User
    {
        return User::factory()->create($attributes);
    }

    protected function createAuthenticatedUser(array $attributes = []): User
    {
        $user = $this->createUser($attributes);
        $this->actingAs($user);
        return $user;
    }

    protected function getPackageProviders($app)
    {
        return [
            \LaravelPlus\FeatureRequests\Providers\FeatureRequestsServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'FeatureRequests' => \LaravelPlus\FeatureRequests\FeatureRequestsFacade::class,
        ];
    }
}
