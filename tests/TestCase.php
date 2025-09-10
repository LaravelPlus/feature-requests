<?php

namespace LaravelPlus\FeatureRequests\Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Load package service provider
        $this->app->register(\LaravelPlus\FeatureRequests\Providers\FeatureRequestsServiceProvider::class);
    }
}