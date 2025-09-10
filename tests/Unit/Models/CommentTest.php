<?php

namespace LaravelPlus\FeatureRequests\Tests\Unit\Models;

use LaravelPlus\FeatureRequests\Models\Comment;
use LaravelPlus\FeatureRequests\Models\FeatureRequest;
use LaravelPlus\FeatureRequests\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_comment_belongs_to_user()
    {
        $user = $this->createUser();
        $comment = Comment::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf('App\Models\User', $comment->user);
        $this->assertEquals($user->id, $comment->user->id);
    }

    public function test_comment_belongs_to_feature_request()
    {
        $featureRequest = FeatureRequest::factory()->create();
        $comment = Comment::factory()->create(['feature_request_id' => $featureRequest->id]);

        $this->assertInstanceOf(FeatureRequest::class, $comment->featureRequest);
        $this->assertEquals($featureRequest->id, $comment->featureRequest->id);
    }

    public function test_comment_belongs_to_parent_comment()
    {
        $parentComment = Comment::factory()->create();
        $childComment = Comment::factory()->create(['parent_id' => $parentComment->id]);

        $this->assertInstanceOf(Comment::class, $childComment->parent);
        $this->assertEquals($parentComment->id, $childComment->parent->id);
    }

    public function test_comment_has_many_replies()
    {
        $parentComment = Comment::factory()->create();
        $replies = Comment::factory()->count(3)->create(['parent_id' => $parentComment->id]);

        $this->assertInstanceOf(Collection::class, $parentComment->replies);
        $this->assertCount(3, $parentComment->replies);
    }

    public function test_comment_uses_soft_deletes()
    {
        $comment = Comment::factory()->create();
        
        $this->assertNull($comment->deleted_at);
        
        $comment->delete();
        
        $this->assertSoftDeleted('feature_request_comments', ['id' => $comment->id]);
    }

    public function test_comment_has_content()
    {
        $content = 'This is a test comment';
        $comment = Comment::factory()->create(['content' => $content]);
        
        $this->assertEquals($content, $comment->content);
    }

    public function test_comment_can_be_approved()
    {
        $comment = Comment::factory()->create(['is_approved' => true]);
        
        $this->assertTrue($comment->is_approved);
    }

    public function test_comment_can_be_pinned()
    {
        $comment = Comment::factory()->create(['is_pinned' => true]);
        
        $this->assertTrue($comment->is_pinned);
    }

    public function test_comment_scope_approved()
    {
        Comment::factory()->create(['is_approved' => true]);
        Comment::factory()->create(['is_approved' => false]);
        
        $approvedComments = Comment::approved()->get();
        
        $this->assertCount(1, $approvedComments);
        $this->assertTrue($approvedComments->first()->is_approved);
    }

    public function test_comment_scope_pinned()
    {
        Comment::factory()->create(['is_pinned' => true]);
        Comment::factory()->create(['is_pinned' => false]);
        
        $pinnedComments = Comment::pinned()->get();
        
        $this->assertCount(1, $pinnedComments);
        $this->assertTrue($pinnedComments->first()->is_pinned);
    }

    public function test_comment_scope_by_user()
    {
        $user1 = $this->createUser();
        $user2 = $this->createUser();
        
        Comment::factory()->create(['user_id' => $user1->id]);
        Comment::factory()->create(['user_id' => $user2->id]);
        
        $user1Comments = Comment::byUser($user1->id)->get();
        
        $this->assertCount(1, $user1Comments);
        $this->assertEquals($user1->id, $user1Comments->first()->user_id);
    }

    public function test_comment_scope_by_feature_request()
    {
        $featureRequest1 = FeatureRequest::factory()->create();
        $featureRequest2 = FeatureRequest::factory()->create();
        
        Comment::factory()->create(['feature_request_id' => $featureRequest1->id]);
        Comment::factory()->create(['feature_request_id' => $featureRequest2->id]);
        
        $featureRequest1Comments = Comment::byFeatureRequest($featureRequest1->id)->get();
        
        $this->assertCount(1, $featureRequest1Comments);
        $this->assertEquals($featureRequest1->id, $featureRequest1Comments->first()->feature_request_id);
    }

    public function test_comment_scope_parent_comments()
    {
        $parentComment = Comment::factory()->create();
        Comment::factory()->create(['parent_id' => $parentComment->id]);
        
        $parentComments = Comment::parentComments()->get();
        
        $this->assertCount(1, $parentComments);
        $this->assertNull($parentComments->first()->parent_id);
    }

    public function test_comment_scope_replies()
    {
        $parentComment = Comment::factory()->create();
        Comment::factory()->create(['parent_id' => $parentComment->id]);
        
        $replies = Comment::replies()->get();
        
        $this->assertCount(1, $replies);
        $this->assertNotNull($replies->first()->parent_id);
    }

    public function test_comment_scope_recent()
    {
        $oldComment = Comment::factory()->create(['created_at' => now()->subDays(10)]);
        $newComment = Comment::factory()->create(['created_at' => now()->subDays(1)]);
        
        $recentComments = Comment::recent()->get();
        
        $this->assertEquals($newComment->id, $recentComments->first()->id);
    }

    public function test_comment_is_reply()
    {
        $parentComment = Comment::factory()->create();
        $childComment = Comment::factory()->create(['parent_id' => $parentComment->id]);
        
        $this->assertFalse($parentComment->isReply());
        $this->assertTrue($childComment->isReply());
    }

    public function test_comment_has_replies()
    {
        $parentComment = Comment::factory()->create();
        Comment::factory()->create(['parent_id' => $parentComment->id]);
        
        $this->assertTrue($parentComment->hasReplies());
    }
}
