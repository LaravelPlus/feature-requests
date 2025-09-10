<?php

namespace LaravelPlus\FeatureRequests\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use LaravelPlus\FeatureRequests\Models\FeatureRequest;
use LaravelPlus\FeatureRequests\Models\Category;
use LaravelPlus\FeatureRequests\Tests\TestCase;

class AdminFeatureRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_index_page_requires_authentication()
    {
        $response = $this->get(route('admin.feature-requests.index'));
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_admin_index()
    {
        $user = \App\Models\User::factory()->create();
        $featureRequest = FeatureRequest::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.feature-requests.index'));

        $response->assertStatus(200);
        $response->assertViewIs('feature-requests::admin.index');
        $response->assertSee($featureRequest->title);
    }

    public function test_admin_index_shows_all_feature_requests()
    {
        $user = \App\Models\User::factory()->create();
        $publicRequest = FeatureRequest::factory()->create(['is_public' => true]);
        $privateRequest = FeatureRequest::factory()->create(['is_public' => false]);

        $response = $this->actingAs($user)->get(route('admin.feature-requests.index'));

        $response->assertSee($publicRequest->title);
        $response->assertSee($privateRequest->title);
    }

    public function test_admin_show_page_requires_authentication()
    {
        $featureRequest = FeatureRequest::factory()->create();

        $response = $this->get(route('admin.feature-requests.show', $featureRequest->id));
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_admin_show()
    {
        $user = \App\Models\User::factory()->create();
        $featureRequest = FeatureRequest::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.feature-requests.show', $featureRequest->id));

        $response->assertStatus(200);
        $response->assertViewIs('feature-requests::admin.show');
        $response->assertSee($featureRequest->title);
        $response->assertSee($featureRequest->description);
    }

    public function test_admin_create_page_requires_authentication()
    {
        $response = $this->get(route('admin.feature-requests.create'));
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_admin_create_form()
    {
        $user = \App\Models\User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.feature-requests.create'));

        $response->assertStatus(200);
        $response->assertViewIs('feature-requests::admin.create');
        $response->assertSee($category->name);
    }

    public function test_authenticated_user_can_create_feature_request_via_admin()
    {
        $user = \App\Models\User::factory()->create();
        $category = Category::factory()->create();

        $data = [
            'title' => 'Admin Created Feature Request',
            'description' => 'This is a detailed description of the feature request.',
            'additional_info' => 'Additional information about the request.',
            'category_id' => $category->id,
            'status' => 'in_progress',
            'priority' => 'high',
            'tags' => ['admin', 'feature'],
            'is_public' => true,
            'is_featured' => true,
        ];

        $response = $this->actingAs($user)->post(route('admin.feature-requests.store'), $data);

        $response->assertRedirect(route('admin.feature-requests.index'));
        $response->assertSessionHas('success', 'Feature request created successfully!');

        $this->assertDatabaseHas('feature_requests', [
            'title' => 'Admin Created Feature Request',
            'description' => 'This is a detailed description of the feature request.',
            'additional_info' => 'Additional information about the request.',
            'category_id' => $category->id,
            'user_id' => $user->id,
            'status' => 'in_progress',
            'is_public' => true,
            'is_featured' => true,
        ]);
    }

    public function test_admin_edit_page_requires_authentication()
    {
        $featureRequest = FeatureRequest::factory()->create();

        $response = $this->get(route('admin.feature-requests.edit', $featureRequest->id));
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_admin_edit_form()
    {
        $user = \App\Models\User::factory()->create();
        $featureRequest = FeatureRequest::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.feature-requests.edit', $featureRequest->id));

        $response->assertStatus(200);
        $response->assertViewIs('feature-requests::admin.edit');
        $response->assertSee($featureRequest->title);
    }

    public function test_authenticated_user_can_update_feature_request()
    {
        $user = \App\Models\User::factory()->create();
        $featureRequest = FeatureRequest::factory()->create([
            'title' => 'Original Title',
            'status' => 'pending',
        ]);

        $data = [
            'title' => 'Updated Title',
            'description' => $featureRequest->description,
            'status' => 'in_progress',
            'is_public' => true,
        ];

        $response = $this->actingAs($user)->put(route('admin.feature-requests.update', $featureRequest->id), $data);

        $response->assertRedirect(route('admin.feature-requests.show', $featureRequest->id));
        $response->assertSessionHas('success', 'Feature request updated successfully!');

        $this->assertDatabaseHas('feature_requests', [
            'id' => $featureRequest->id,
            'title' => 'Updated Title',
            'status' => 'in_progress',
        ]);
    }

    public function test_authenticated_user_can_delete_feature_request()
    {
        $user = \App\Models\User::factory()->create();
        $featureRequest = FeatureRequest::factory()->create();

        $response = $this->actingAs($user)->delete(route('admin.feature-requests.destroy', $featureRequest->id));

        $response->assertRedirect(route('admin.feature-requests.index'));
        $response->assertSessionHas('success', 'Feature request deleted successfully!');

        $this->assertSoftDeleted('feature_requests', [
            'id' => $featureRequest->id,
        ]);
    }

    public function test_admin_index_shows_statistics()
    {
        $user = \App\Models\User::factory()->create();
        
        FeatureRequest::factory()->create(['status' => 'pending']);
        FeatureRequest::factory()->create(['status' => 'in_progress']);
        FeatureRequest::factory()->create(['status' => 'completed']);

        $response = $this->actingAs($user)->get(route('admin.feature-requests.index'));

        $response->assertSee('3'); // Total feature requests
        $response->assertSee('1'); // Pending
        $response->assertSee('1'); // In Progress
        $response->assertSee('1'); // Completed
    }

    public function test_admin_search_functionality()
    {
        $user = \App\Models\User::factory()->create();
        $featureRequest1 = FeatureRequest::factory()->create(['title' => 'Search Feature']);
        $featureRequest2 = FeatureRequest::factory()->create(['title' => 'Another Feature']);

        $response = $this->actingAs($user)->get(route('admin.feature-requests.index', ['search' => 'Search']));

        $response->assertSee($featureRequest1->title);
        $response->assertDontSee($featureRequest2->title);
    }

    public function test_admin_status_filter()
    {
        $user = \App\Models\User::factory()->create();
        $featureRequest1 = FeatureRequest::factory()->create(['status' => 'pending']);
        $featureRequest2 = FeatureRequest::factory()->create(['status' => 'completed']);

        $response = $this->actingAs($user)->get(route('admin.feature-requests.index', ['status' => 'pending']));

        $response->assertSee($featureRequest1->title);
        $response->assertDontSee($featureRequest2->title);
    }

    public function test_admin_category_filter()
    {
        $user = \App\Models\User::factory()->create();
        $category1 = Category::factory()->create(['name' => 'UI/UX']);
        $category2 = Category::factory()->create(['name' => 'Backend']);
        
        $featureRequest1 = FeatureRequest::factory()->create(['category_id' => $category1->id]);
        $featureRequest2 = FeatureRequest::factory()->create(['category_id' => $category2->id]);

        $response = $this->actingAs($user)->get(route('admin.feature-requests.index', ['category' => $category1->id]));

        $response->assertSee($featureRequest1->title);
        $response->assertDontSee($featureRequest2->title);
    }

    public function test_admin_featured_filter()
    {
        $user = \App\Models\User::factory()->create();
        $featureRequest1 = FeatureRequest::factory()->create(['is_featured' => true]);
        $featureRequest2 = FeatureRequest::factory()->create(['is_featured' => false]);

        $response = $this->actingAs($user)->get(route('admin.feature-requests.index', ['featured' => '1']));

        $response->assertSee($featureRequest1->title);
        $response->assertDontSee($featureRequest2->title);
    }
}
