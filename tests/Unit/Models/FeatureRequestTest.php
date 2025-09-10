<?php

namespace LaravelPlus\FeatureRequests\Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use LaravelPlus\FeatureRequests\Models\FeatureRequest;
use LaravelPlus\FeatureRequests\Models\Category;
use LaravelPlus\FeatureRequests\Models\Vote;
use LaravelPlus\FeatureRequests\Models\Comment;
use LaravelPlus\FeatureRequests\Tests\TestCase;

class FeatureRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_feature_request_can_be_created()
    {
        $featureRequest = FeatureRequest::factory()->create([
            'title' => 'Test Feature',
            'description' => 'This is a test feature request',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('feature_requests', [
            'title' => 'Test Feature',
            'description' => 'This is a test feature request',
            'status' => 'pending',
        ]);

        $this->assertEquals('Test Feature', $featureRequest->title);
        $this->assertEquals('This is a test feature request', $featureRequest->description);
        $this->assertEquals('pending', $featureRequest->status);
    }

    public function test_feature_request_belongs_to_user()
    {
        $user = \App\Models\User::factory()->create();
        $featureRequest = FeatureRequest::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(\App\Models\User::class, $featureRequest->user);
        $this->assertEquals($user->id, $featureRequest->user->id);
    }

    public function test_feature_request_belongs_to_category()
    {
        $category = Category::factory()->create(['name' => 'UI/UX']);
        $featureRequest = FeatureRequest::factory()->create(['category_id' => $category->id]);

        $this->assertInstanceOf(Category::class, $featureRequest->category);
        $this->assertEquals($category->id, $featureRequest->category->id);
    }

    public function test_feature_request_has_many_votes()
    {
        $featureRequest = FeatureRequest::factory()->create();
        $user1 = \App\Models\User::factory()->create();
        $user2 = \App\Models\User::factory()->create();

        Vote::factory()->create([
            'feature_request_id' => $featureRequest->id,
            'user_id' => $user1->id,
            'vote_type' => 'up'
        ]);

        Vote::factory()->create([
            'feature_request_id' => $featureRequest->id,
            'user_id' => $user2->id,
            'vote_type' => 'down'
        ]);

        $this->assertCount(2, $featureRequest->votes);
        $this->assertInstanceOf(Vote::class, $featureRequest->votes->first());
    }

    public function test_feature_request_has_many_comments()
    {
        $featureRequest = FeatureRequest::factory()->create();
        $user = \App\Models\User::factory()->create();

        Comment::factory()->count(3)->create([
            'feature_request_id' => $featureRequest->id,
            'user_id' => $user->id,
        ]);

        $this->assertCount(3, $featureRequest->comments);
        $this->assertInstanceOf(Comment::class, $featureRequest->comments->first());
    }

    public function test_feature_request_updates_vote_count()
    {
        $featureRequest = FeatureRequest::factory()->create([
            'up_votes' => 0,
            'down_votes' => 0,
            'vote_count' => 0,
        ]);

        $user1 = \App\Models\User::factory()->create();
        $user2 = \App\Models\User::factory()->create();
        $user3 = \App\Models\User::factory()->create();

        // Add 2 upvotes and 1 downvote
        Vote::factory()->create([
            'feature_request_id' => $featureRequest->id,
            'user_id' => $user1->id,
            'vote_type' => 'up'
        ]);

        Vote::factory()->create([
            'feature_request_id' => $featureRequest->id,
            'user_id' => $user2->id,
            'vote_type' => 'up'
        ]);

        Vote::factory()->create([
            'feature_request_id' => $featureRequest->id,
            'user_id' => $user3->id,
            'vote_type' => 'down'
        ]);

        $featureRequest->updateVoteCount();

        $this->assertEquals(2, $featureRequest->fresh()->up_votes);
        $this->assertEquals(1, $featureRequest->fresh()->down_votes);
        $this->assertEquals(3, $featureRequest->fresh()->vote_count);
    }

    public function test_feature_request_has_slug()
    {
        $featureRequest = FeatureRequest::factory()->create([
            'title' => 'Test Feature Request'
        ]);

        $this->assertNotNull($featureRequest->slug);
        $this->assertStringContainsString('test-feature-request', $featureRequest->slug);
    }

    public function test_feature_request_can_be_soft_deleted()
    {
        $featureRequest = FeatureRequest::factory()->create();
        $featureRequest->delete();

        $this->assertSoftDeleted('feature_requests', [
            'id' => $featureRequest->id
        ]);
    }

    public function test_feature_request_scope_public()
    {
        FeatureRequest::factory()->create(['is_public' => true]);
        FeatureRequest::factory()->create(['is_public' => false]);

        $publicRequests = FeatureRequest::public()->get();

        $this->assertCount(1, $publicRequests);
        $this->assertTrue($publicRequests->first()->is_public);
    }

    public function test_feature_request_scope_featured()
    {
        FeatureRequest::factory()->create(['is_featured' => true]);
        FeatureRequest::factory()->create(['is_featured' => false]);

        $featuredRequests = FeatureRequest::featured()->get();

        $this->assertCount(1, $featuredRequests);
        $this->assertTrue($featuredRequests->first()->is_featured);
    }

    public function test_feature_request_scope_by_status()
    {
        FeatureRequest::factory()->create(['status' => 'pending']);
        FeatureRequest::factory()->create(['status' => 'in_progress']);
        FeatureRequest::factory()->create(['status' => 'completed']);

        $pendingRequests = FeatureRequest::byStatus('pending')->get();
        $this->assertCount(1, $pendingRequests);
        $this->assertEquals('pending', $pendingRequests->first()->status);
    }

    public function test_feature_request_scope_by_category()
    {
        $category = Category::factory()->create();
        FeatureRequest::factory()->create(['category_id' => $category->id]);
        FeatureRequest::factory()->create(['category_id' => null]);

        $categoryRequests = FeatureRequest::byCategory($category->id)->get();
        $this->assertCount(1, $categoryRequests);
        $this->assertEquals($category->id, $categoryRequests->first()->category_id);
    }

    public function test_feature_request_scope_popular()
    {
        $popular = FeatureRequest::factory()->create(['up_votes' => 10]);
        $notPopular = FeatureRequest::factory()->create(['up_votes' => 1]);

        $popularRequests = FeatureRequest::popular()->get();
        $this->assertCount(1, $popularRequests);
        $this->assertEquals($popular->id, $popularRequests->first()->id);
    }
}