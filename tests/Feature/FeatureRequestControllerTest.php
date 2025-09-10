<?php

namespace LaravelPlus\FeatureRequests\Tests\Feature;

use LaravelPlus\FeatureRequests\Models\FeatureRequest;
use LaravelPlus\FeatureRequests\Models\Category;
use LaravelPlus\FeatureRequests\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FeatureRequestControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_feature_requests_index()
    {
        $featureRequest = FeatureRequest::factory()->create();

        $response = $this->get(route('feature-requests.index'));

        $response->assertStatus(200);
        $response->assertViewIs('feature-requests::index');
        $response->assertSee($featureRequest->title);
    }

    public function test_can_view_feature_request_create_form()
    {
        $response = $this->get(route('feature-requests.create'));

        $response->assertStatus(200);
        $response->assertViewIs('feature-requests::create');
    }

    public function test_can_create_feature_request()
    {
        $user = $this->createAuthenticatedUser();
        $category = Category::factory()->create();

        $data = [
            'title' => 'Test Feature Request',
            'description' => 'This is a test feature request description.',
            'category_id' => $category->id,
            'priority' => 'medium',
            'tags' => 'test,feature',
            'is_public' => true,
            'estimated_effort' => 'small',
        ];

        $response = $this->post(route('feature-requests.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('feature_requests', [
            'title' => 'Test Feature Request',
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);
    }

    public function test_can_view_feature_request_show()
    {
        $featureRequest = FeatureRequest::factory()->create();

        $response = $this->get(route('feature-requests.show', $featureRequest->slug));

        $response->assertStatus(200);
        $response->assertViewIs('feature-requests::show');
        $response->assertSee($featureRequest->title);
    }

    public function test_can_view_feature_request_edit_form()
    {
        $user = $this->createAuthenticatedUser();
        $featureRequest = FeatureRequest::factory()->create(['user_id' => $user->id]);

        $response = $this->get(route('feature-requests.edit', $featureRequest->slug));

        $response->assertStatus(200);
        $response->assertViewIs('feature-requests::edit');
        $response->assertSee($featureRequest->title);
    }

    public function test_can_update_feature_request()
    {
        $user = $this->createAuthenticatedUser();
        $featureRequest = FeatureRequest::factory()->create(['user_id' => $user->id]);

        $data = [
            'title' => 'Updated Feature Request',
            'description' => 'Updated description.',
            'priority' => 'high',
        ];

        $response = $this->put(route('feature-requests.update', $featureRequest->slug), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('feature_requests', [
            'id' => $featureRequest->id,
            'title' => 'Updated Feature Request',
            'priority' => 'high',
        ]);
    }

    public function test_can_delete_feature_request()
    {
        $user = $this->createAuthenticatedUser();
        $featureRequest = FeatureRequest::factory()->create(['user_id' => $user->id]);

        $response = $this->delete(route('feature-requests.destroy', $featureRequest->slug));

        $response->assertRedirect();
        $this->assertSoftDeleted('feature_requests', ['id' => $featureRequest->id]);
    }

    public function test_requires_authentication_for_create()
    {
        $response = $this->get(route('feature-requests.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_requires_authentication_for_store()
    {
        $data = [
            'title' => 'Test Feature Request',
            'description' => 'This is a test feature request description.',
        ];

        $response = $this->post(route('feature-requests.store'), $data);

        $response->assertRedirect(route('login'));
    }

    public function test_requires_authentication_for_edit()
    {
        $featureRequest = FeatureRequest::factory()->create();

        $response = $this->get(route('feature-requests.edit', $featureRequest->slug));

        $response->assertRedirect(route('login'));
    }

    public function test_requires_authentication_for_update()
    {
        $featureRequest = FeatureRequest::factory()->create();

        $data = [
            'title' => 'Updated Feature Request',
            'description' => 'Updated description.',
        ];

        $response = $this->put(route('feature-requests.update', $featureRequest->slug), $data);

        $response->assertRedirect(route('login'));
    }

    public function test_requires_authentication_for_destroy()
    {
        $featureRequest = FeatureRequest::factory()->create();

        $response = $this->delete(route('feature-requests.destroy', $featureRequest->slug));

        $response->assertRedirect(route('login'));
    }

    public function test_can_filter_feature_requests_by_status()
    {
        FeatureRequest::factory()->create(['status' => 'pending']);
        FeatureRequest::factory()->create(['status' => 'in_progress']);

        $response = $this->get(route('feature-requests.index', ['status' => 'pending']));

        $response->assertStatus(200);
        $response->assertSee('pending');
    }

    public function test_can_filter_feature_requests_by_priority()
    {
        FeatureRequest::factory()->create(['priority' => 'high']);
        FeatureRequest::factory()->create(['priority' => 'low']);

        $response = $this->get(route('feature-requests.index', ['priority' => 'high']));

        $response->assertStatus(200);
    }

    public function test_can_search_feature_requests()
    {
        FeatureRequest::factory()->create(['title' => 'Searchable Feature']);
        FeatureRequest::factory()->create(['title' => 'Another Feature']);

        $response = $this->get(route('feature-requests.index', ['search' => 'Searchable']));

        $response->assertStatus(200);
        $response->assertSee('Searchable Feature');
    }

    public function test_validation_requires_title()
    {
        $user = $this->createAuthenticatedUser();

        $data = [
            'description' => 'This is a test feature request description.',
        ];

        $response = $this->post(route('feature-requests.store'), $data);

        $response->assertSessionHasErrors(['title']);
    }

    public function test_validation_requires_description()
    {
        $user = $this->createAuthenticatedUser();

        $data = [
            'title' => 'Test Feature Request',
        ];

        $response = $this->post(route('feature-requests.store'), $data);

        $response->assertSessionHasErrors(['description']);
    }

    public function test_validation_title_max_length()
    {
        $user = $this->createAuthenticatedUser();

        $data = [
            'title' => str_repeat('a', 256),
            'description' => 'This is a test feature request description.',
        ];

        $response = $this->post(route('feature-requests.store'), $data);

        $response->assertSessionHasErrors(['title']);
    }

    public function test_validation_description_max_length()
    {
        $user = $this->createAuthenticatedUser();

        $data = [
            'title' => 'Test Feature Request',
            'description' => str_repeat('a', 10001),
        ];

        $response = $this->post(route('feature-requests.store'), $data);

        $response->assertSessionHasErrors(['description']);
    }

    public function test_validation_priority_must_be_valid()
    {
        $user = $this->createAuthenticatedUser();

        $data = [
            'title' => 'Test Feature Request',
            'description' => 'This is a test feature request description.',
            'priority' => 'invalid_priority',
        ];

        $response = $this->post(route('feature-requests.store'), $data);

        $response->assertSessionHasErrors(['priority']);
    }

    public function test_validation_status_must_be_valid()
    {
        $user = $this->createAuthenticatedUser();
        $featureRequest = FeatureRequest::factory()->create(['user_id' => $user->id]);

        $data = [
            'title' => 'Updated Feature Request',
            'description' => 'Updated description.',
            'status' => 'invalid_status',
        ];

        $response = $this->put(route('feature-requests.update', $featureRequest->slug), $data);

        $response->assertSessionHasErrors(['status']);
    }

    public function test_can_view_public_feature_requests_without_auth()
    {
        $publicRequest = FeatureRequest::factory()->create(['is_public' => true]);
        $privateRequest = FeatureRequest::factory()->create(['is_public' => false]);

        $response = $this->get(route('feature-requests.index'));

        $response->assertStatus(200);
        $response->assertSee($publicRequest->title);
    }

    public function test_cannot_view_private_feature_requests_without_auth()
    {
        $privateRequest = FeatureRequest::factory()->create(['is_public' => false]);

        $response = $this->get(route('feature-requests.show', $privateRequest->slug));

        $response->assertStatus(403);
    }

    public function test_can_view_own_private_feature_requests()
    {
        $user = $this->createAuthenticatedUser();
        $privateRequest = FeatureRequest::factory()->create([
            'user_id' => $user->id,
            'is_public' => false
        ]);

        $response = $this->get(route('feature-requests.show', $privateRequest->slug));

        $response->assertStatus(200);
        $response->assertSee($privateRequest->title);
    }
}
