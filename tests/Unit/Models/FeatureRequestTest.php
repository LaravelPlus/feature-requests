<?php

namespace LaravelPlus\FeatureRequests\Tests\Unit\Models;

use LaravelPlus\FeatureRequests\Models\FeatureRequest;
use LaravelPlus\FeatureRequests\Models\Category;
use LaravelPlus\FeatureRequests\Models\Vote;
use LaravelPlus\FeatureRequests\Models\Comment;
use LaravelPlus\FeatureRequests\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection;

class FeatureRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_feature_request_belongs_to_user()
    {
        $user = $this->createUser();
        $featureRequest = FeatureRequest::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf('App\Models\User', $featureRequest->user);
        $this->assertEquals($user->id, $featureRequest->user->id);
    }

    public function test_feature_request_belongs_to_category()
    {
        $category = Category::factory()->create();
        $featureRequest = FeatureRequest::factory()->create(['category_id' => $category->id]);

        $this->assertInstanceOf(Category::class, $featureRequest->category);
        $this->assertEquals($category->id, $featureRequest->category->id);
    }

    public function test_feature_request_has_many_votes()
    {
        $featureRequest = FeatureRequest::factory()->create();
        $votes = Vote::factory()->count(3)->create(['feature_request_id' => $featureRequest->id]);

        $this->assertInstanceOf(Collection::class, $featureRequest->votes);
        $this->assertCount(3, $featureRequest->votes);
    }

    public function test_feature_request_has_many_comments()
    {
        $featureRequest = FeatureRequest::factory()->create();
        $comments = Comment::factory()->count(2)->create(['feature_request_id' => $featureRequest->id]);

        $this->assertInstanceOf(Collection::class, $featureRequest->comments);
        $this->assertCount(2, $featureRequest->comments);
    }

    public function test_feature_request_uses_soft_deletes()
    {
        $featureRequest = FeatureRequest::factory()->create();
        
        $this->assertNull($featureRequest->deleted_at);
        
        $featureRequest->delete();
        
        $this->assertSoftDeleted('feature_requests', ['id' => $featureRequest->id]);
    }

    public function test_feature_request_has_slug()
    {
        $featureRequest = FeatureRequest::factory()->create(['title' => 'Test Feature Request']);
        
        $this->assertNotNull($featureRequest->slug);
        $this->assertEquals('test-feature-request', $featureRequest->slug);
    }

    public function test_feature_request_can_be_featured()
    {
        $featureRequest = FeatureRequest::factory()->create(['is_featured' => true]);
        
        $this->assertTrue($featureRequest->is_featured);
    }

    public function test_feature_request_can_be_public()
    {
        $featureRequest = FeatureRequest::factory()->create(['is_public' => true]);
        
        $this->assertTrue($featureRequest->is_public);
    }

    public function test_feature_request_has_status()
    {
        $featureRequest = FeatureRequest::factory()->create(['status' => 'in_progress']);
        
        $this->assertEquals('in_progress', $featureRequest->status);
    }

    public function test_feature_request_has_priority()
    {
        $featureRequest = FeatureRequest::factory()->create(['priority' => 'high']);
        
        $this->assertEquals('high', $featureRequest->priority);
    }

    public function test_feature_request_has_estimated_effort()
    {
        $featureRequest = FeatureRequest::factory()->create(['estimated_effort' => 'medium']);
        
        $this->assertEquals('medium', $featureRequest->estimated_effort);
    }

    public function test_feature_request_has_tags()
    {
        $featureRequest = FeatureRequest::factory()->create(['tags' => 'ui,ux,mobile']);
        
        $this->assertEquals('ui,ux,mobile', $featureRequest->tags);
    }

    public function test_feature_request_has_due_date()
    {
        $dueDate = now()->addDays(30);
        $featureRequest = FeatureRequest::factory()->create(['due_date' => $dueDate]);
        
        $this->assertEquals($dueDate->format('Y-m-d'), $featureRequest->due_date->format('Y-m-d'));
    }

    public function test_feature_request_vote_count_defaults_to_zero()
    {
        $featureRequest = FeatureRequest::factory()->create();
        
        $this->assertEquals(0, $featureRequest->vote_count);
    }

    public function test_feature_request_comment_count_defaults_to_zero()
    {
        $featureRequest = FeatureRequest::factory()->create();
        
        $this->assertEquals(0, $featureRequest->comment_count);
    }

    public function test_feature_request_view_count_defaults_to_zero()
    {
        $featureRequest = FeatureRequest::factory()->create();
        
        $this->assertEquals(0, $featureRequest->view_count);
    }

    public function test_feature_request_can_be_assigned_to_user()
    {
        $assignedUser = $this->createUser();
        $featureRequest = FeatureRequest::factory()->create(['assigned_to' => $assignedUser->id]);
        
        $this->assertEquals($assignedUser->id, $featureRequest->assigned_to);
    }

    public function test_feature_request_scope_published()
    {
        FeatureRequest::factory()->create(['is_public' => true]);
        FeatureRequest::factory()->create(['is_public' => false]);
        
        $publishedRequests = FeatureRequest::published()->get();
        
        $this->assertCount(1, $publishedRequests);
        $this->assertTrue($publishedRequests->first()->is_public);
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
        $inProgressRequests = FeatureRequest::byStatus('in_progress')->get();
        
        $this->assertCount(1, $pendingRequests);
        $this->assertCount(1, $inProgressRequests);
        $this->assertEquals('pending', $pendingRequests->first()->status);
        $this->assertEquals('in_progress', $inProgressRequests->first()->status);
    }

    public function test_feature_request_scope_by_priority()
    {
        FeatureRequest::factory()->create(['priority' => 'high']);
        FeatureRequest::factory()->create(['priority' => 'medium']);
        FeatureRequest::factory()->create(['priority' => 'low']);
        
        $highPriorityRequests = FeatureRequest::byPriority('high')->get();
        $mediumPriorityRequests = FeatureRequest::byPriority('medium')->get();
        
        $this->assertCount(1, $highPriorityRequests);
        $this->assertCount(1, $mediumPriorityRequests);
        $this->assertEquals('high', $highPriorityRequests->first()->priority);
        $this->assertEquals('medium', $mediumPriorityRequests->first()->priority);
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

    public function test_feature_request_scope_most_voted()
    {
        $featureRequest1 = FeatureRequest::factory()->create(['vote_count' => 10]);
        $featureRequest2 = FeatureRequest::factory()->create(['vote_count' => 5]);
        $featureRequest3 = FeatureRequest::factory()->create(['vote_count' => 15]);
        
        $mostVoted = FeatureRequest::mostVoted()->first();
        
        $this->assertEquals($featureRequest3->id, $mostVoted->id);
        $this->assertEquals(15, $mostVoted->vote_count);
    }

    public function test_feature_request_scope_recent()
    {
        $oldRequest = FeatureRequest::factory()->create(['created_at' => now()->subDays(10)]);
        $newRequest = FeatureRequest::factory()->create(['created_at' => now()->subDays(1)]);
        
        $recentRequests = FeatureRequest::recent()->get();
        
        $this->assertEquals($newRequest->id, $recentRequests->first()->id);
    }
}
