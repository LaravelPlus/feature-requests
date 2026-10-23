<?php

namespace LaravelPlus\FeatureRequests\Tests\Feature;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use LaravelPlus\FeatureRequests\Providers\FeatureRequestsServiceProvider;
use Orchestra\Testbench\TestCase;

// Self-contained (the legacy tests need a host app): run with `vendor/bin/phpunit tests/Feature/AccessControlTest.php`
class AccessControlTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [FeatureRequestsServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('app.key', 'base64:' . base64_encode(random_bytes(32)));
        $app['config']->set('feature-requests.cache.enabled', false);
        $app['config']->set('auth.providers.users.model', User::class);
    }

    protected function defineDatabaseMigrations(): void
    {
        Schema::create('users', function ($t): void {
            $t->id();
            $t->string('name');
            $t->string('email');
            $t->string('password');
            $t->timestamps();
        });
        $this->loadMigrationsFrom(__DIR__ . '/../../src/Database/Migrations');
    }

    protected function defineRoutes($router): void
    {
        // layouts reference host-app routes
        $router->post('/logout', fn () => '')->name('logout');
    }

    private function user(): User
    {
        return User::forceCreate(['name' => 'u', 'email' => uniqid() . '@x.test', 'password' => 'x']);
    }

    private function request(int $ownerId, bool $public, string $slug): void
    {
        DB::table('feature_requests')->insert([
            'title' => $slug, 'slug' => $slug, 'uuid' => (string) \Illuminate\Support\Str::uuid(), 'description' => 'd',
            'user_id' => $ownerId, 'is_public' => $public, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    public function test_fresh_install_migrates_and_down_is_safe(): void
    {
        $this->assertTrue(Schema::hasColumn('feature_requests', 'slug'));
        $this->assertFalse(Schema::hasColumn('feature_requests', 'status_id'));
    }

    public function test_admin_pages_are_404_without_permission_and_open_with_it(): void
    {
        $this->actingAs($this->user());

        $this->get('/admin/feature-requests')->assertNotFound();
        $this->getJson('/admin/feature-requests/reports/statistics')->assertNotFound();

        Gate::define('manage feature requests', fn ($u = null) => true);
        $this->getJson('/admin/feature-requests/reports/statistics')->assertOk();
    }

    public function test_customer_cannot_open_non_public_request_but_owner_can(): void
    {
        $owner = $this->user();
        $this->request($owner->id, false, 'secret');

        $this->actingAs($this->user())->get('/feature-requests/secret')->assertNotFound();
        $this->actingAs($owner)->get('/feature-requests/secret')->assertOk();
    }

    public function test_api_requires_auth_and_hides_non_public_requests(): void
    {
        $owner = $this->user();
        $this->request($owner->id, true, 'open-one');
        $this->request($owner->id, false, 'closed-one');

        $this->getJson('/api/feature-requests')->assertUnauthorized();

        $other = $this->actingAs($this->user());
        $other->getJson('/api/feature-requests/closed-one')->assertNotFound();
        $other->getJson('/api/feature-requests/statistics')->assertNotFound();
        $slugs = collect($other->getJson('/api/feature-requests')->assertOk()->json('data'))->pluck('slug');
        $this->assertTrue($slugs->contains('open-one'));
        $this->assertFalse($slugs->contains('closed-one'));
    }
}
