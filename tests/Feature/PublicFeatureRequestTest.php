<?php

namespace LaravelPlus\FeatureRequests\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use LaravelPlus\FeatureRequests\Models\FeatureRequest;
use LaravelPlus\FeatureRequests\Models\Category;
use LaravelPlus\FeatureRequests\Models\Vote;
use LaravelPlus\FeatureRequests\Tests\TestCase;

class PublicFeatureRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_index_page_requires_authentication()
    {
        $response = $this->get(route('feature-requests.index'));
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_public_index()
    {
        $user = \App\Models\User::factory()->create();
        $featureRequest = FeatureRequest::factory()->create(['is_public' => true]);

        $response = $this->actingAs($user)->get(route('feature-requests.index'));

        $response->assertStatus(200);
        $response->assertViewIs('feature-requests::public.index');
        $response->assertSee($featureRequest->title);
    }

    public function test_public_index_shows_only_public_feature_requests()
    {
        $user = \App\Models\User::factory()->create();
        $publicRequest = FeatureRequest::factory()->create(['is_public' => true]);
        $privateRequest = FeatureRequest::factory()->create(['is_public' => false]);

        $response = $this->actingAs($user)->get(route('feature-requests.index'));

        $response->assertSee($publicRequest->title);
        $response->assertDontSee($privateRequest->title);
    }

    public function test_public_show_page_requires_authentication()
    {
        $featureRequest = FeatureRequest::factory()->create();

        $response = $this->get(route('feature-requests.show', $featureRequest->slug));
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_public_show()
    {
        $user = \App\Models\User::factory()->create();
        $featureRequest = FeatureRequest::factory()->create(['is_public' => true]);

        $response = $this->actingAs($user)->get(route('feature-requests.show', $featureRequest->slug));

        $response->assertStatus(200);
        $response->assertViewIs('feature-requests::public.show');
        $response->assertSee($featureRequest->title);
        $response->assertSee($featureRequest->description);
    }

    public function test_public_show_increments_view_count()
    {
        $user = \App\Models\User::factory()->create();
        $featureRequest = FeatureRequest::factory()->create(['view_count' => 0]);

        $this->actingAs($user)->get(route('feature-requests.show', $featureRequest->slug));

        $featureRequest->refresh();
        $this->assertEquals(1, $featureRequest->view_count);
    }

    public function test_public_create_page_requires_authentication()
    {
        $response = $this->get(route('feature-requests.create'));
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_create_form()
    {
        $user = \App\Models\User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->get(route('feature-requests.create'));

        $response->assertStatus(200);
        $response->assertViewIs('feature-requests::public.create');
        $response->assertSee($category->name);
    }

    public function test_authenticated_user_can_create_feature_request()
    {
        $user = \App\Models\User::factory()->create();
        $category = Category::factory()->create();

        $data = [
            'title' => 'New Feature Request',
            'description' => 'This is a detailed description of the feature request.',
            'additional_info' => 'Additional information about the request.',
            'category_id' => $category->id,
            'tags' => ['ui', 'ux'],
            'is_public' => true,
        ];

        $response = $this->actingAs($user)->post(route('feature-requests.store'), $data);

        $response->assertRedirect(route('feature-requests.index'));
        $response->assertSessionHas('success', 'Feature request created successfully!');

        $this->assertDatabaseHas('feature_requests', [
            'title' => 'New Feature Request',
            'description' => 'This is a detailed description of the feature request.',
            'additional_info' => 'Additional information about the request.',
            'category_id' => $category->id,
            'user_id' => $user->id,
            'status' => 'pending',
            'is_public' => true,
        ]);
    }

    public function test_feature_request_creation_requires_title()
    {
        $user = \App\Models\User::factory()->create();

        $data = [
            'description' => 'This is a detailed description.',
        ];

        $response = $this->actingAs($user)->post(route('feature-requests.store'), $data);

        $response->assertSessionHasErrors(['title']);
    }

    public function test_feature_request_creation_requires_description()
    {
        $user = \App\Models\User::factory()->create();

        $data = [
            'title' => 'New Feature Request',
        ];

        $response = $this->actingAs($user)->post(route('feature-requests.store'), $data);

        $response->assertSessionHasErrors(['description']);
    }

    public function test_feature_request_creation_requires_minimum_description_length()
    {
        $user = \App\Models\User::factory()->create();

        $data = [
            'title' => 'New Feature Request',
            'description' => 'Short',
        ];

        $response = $this->actingAs($user)->post(route('feature-requests.store'), $data);

        $response->assertSessionHasErrors(['description']);
    }

    public function test_authenticated_user_can_vote_on_feature_request()
    {
        $user = \App\Models\User::factory()->create();
        $featureRequest = FeatureRequest::factory()->create();

        $response = $this->actingAs($user)->post(route('feature-requests.vote', $featureRequest->slug), [
            'vote_type' => 'up'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Vote recorded successfully.',
        ]);

        $this->assertDatabaseHas('feature_request_votes', [
            'feature_request_id' => $featureRequest->id,
            'user_id' => $user->id,
            'vote_type' => 'up',
        ]);
    }

    public function test_user_can_change_vote()
    {
        $user = \App\Models\User::factory()->create();
        $featureRequest = FeatureRequest::factory()->create();

        // First vote up
        $this->actingAs($user)->post(route('feature-requests.vote', $featureRequest->slug), [
            'vote_type' => 'up'
        ]);

        // Change to down vote
        $response = $this->actingAs($user)->post(route('feature-requests.vote', $featureRequest->slug), [
            'vote_type' => 'down'
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('feature_request_votes', [
            'feature_request_id' => $featureRequest->id,
            'user_id' => $user->id,
            'vote_type' => 'down',
        ]);

        // Should only have one vote record
        $this->assertDatabaseCount('feature_request_votes', 1);
    }

    public function test_voting_requires_authentication()
    {
        $featureRequest = FeatureRequest::factory()->create();

        $response = $this->post(route('feature-requests.vote', $featureRequest->slug), [
            'vote_type' => 'up'
        ]);

        $response->assertRedirect('/login');
    }

    public function test_voting_requires_valid_vote_type()
    {
        $user = \App\Models\User::factory()->create();
        $featureRequest = FeatureRequest::factory()->create();

        $response = $this->actingAs($user)->post(route('feature-requests.vote', $featureRequest->slug), [
            'vote_type' => 'invalid'
        ]);

        $response->assertSessionHasErrors(['vote_type']);
    }

    public function test_public_index_shows_vote_counts()
    {
        $user = \App\Models\User::factory()->create();
        $featureRequest = FeatureRequest::factory()->create([
            'up_votes' => 5,
            'down_votes' => 2,
            'vote_count' => 7,
        ]);

        $response = $this->actingAs($user)->get(route('feature-requests.index'));

        $response->assertSee('3'); // Net votes (5-2)
        $response->assertSee('7 votes'); // Total votes
    }

    public function test_public_index_shows_user_vote_status()
    {
        $user = \App\Models\User::factory()->create();
        $featureRequest = FeatureRequest::factory()->create();

        // User votes up
        $this->actingAs($user)->post(route('feature-requests.vote', $featureRequest->slug), [
            'vote_type' => 'up'
        ]);

        $response = $this->actingAs($user)->get(route('feature-requests.index'));

        $response->assertSee('Upvoted');
    }

    public function test_search_functionality()
    {
        $user = \App\Models\User::factory()->create();
        $featureRequest1 = FeatureRequest::factory()->create(['title' => 'Search Feature']);
        $featureRequest2 = FeatureRequest::factory()->create(['title' => 'Another Feature']);

        $response = $this->actingAs($user)->get(route('feature-requests.index', ['search' => 'Search']));

        $response->assertSee($featureRequest1->title);
        $response->assertDontSee($featureRequest2->title);
    }

    public function test_category_filter()
    {
        $user = \App\Models\User::factory()->create();
        $category1 = Category::factory()->create(['name' => 'UI/UX']);
        $category2 = Category::factory()->create(['name' => 'Backend']);
        
        $featureRequest1 = FeatureRequest::factory()->create(['category_id' => $category1->id]);
        $featureRequest2 = FeatureRequest::factory()->create(['category_id' => $category2->id]);

        $response = $this->actingAs($user)->get(route('feature-requests.index', ['category' => $category1->id]));

        $response->assertSee($featureRequest1->title);
        $response->assertDontSee($featureRequest2->title);
    }
}
