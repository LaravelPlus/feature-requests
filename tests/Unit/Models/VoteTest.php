<?php

namespace LaravelPlus\FeatureRequests\Tests\Unit\Models;

use LaravelPlus\FeatureRequests\Models\Vote;
use LaravelPlus\FeatureRequests\Models\FeatureRequest;
use LaravelPlus\FeatureRequests\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_vote_belongs_to_user()
    {
        $user = $this->createUser();
        $vote = Vote::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf('App\Models\User', $vote->user);
        $this->assertEquals($user->id, $vote->user->id);
    }

    public function test_vote_belongs_to_feature_request()
    {
        $featureRequest = FeatureRequest::factory()->create();
        $vote = Vote::factory()->create(['feature_request_id' => $featureRequest->id]);

        $this->assertInstanceOf(FeatureRequest::class, $vote->featureRequest);
        $this->assertEquals($featureRequest->id, $vote->featureRequest->id);
    }

    public function test_vote_has_unique_user_feature_request_constraint()
    {
        $user = $this->createUser();
        $featureRequest = FeatureRequest::factory()->create();
        
        // Create first vote
        Vote::factory()->create([
            'user_id' => $user->id,
            'feature_request_id' => $featureRequest->id
        ]);

        // Try to create duplicate vote
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Vote::factory()->create([
            'user_id' => $user->id,
            'feature_request_id' => $featureRequest->id
        ]);
    }

    public function test_vote_has_vote_type()
    {
        $vote = Vote::factory()->create(['vote_type' => 'up']);
        
        $this->assertEquals('up', $vote->vote_type);
    }

    public function test_vote_defaults_to_up_type()
    {
        $vote = Vote::factory()->create();
        
        $this->assertEquals('up', $vote->vote_type);
    }

    public function test_vote_can_be_down_vote()
    {
        $vote = Vote::factory()->create(['vote_type' => 'down']);
        
        $this->assertEquals('down', $vote->vote_type);
    }

    public function test_vote_scope_by_type()
    {
        Vote::factory()->create(['vote_type' => 'up']);
        Vote::factory()->create(['vote_type' => 'down']);
        
        $upVotes = Vote::byType('up')->get();
        $downVotes = Vote::byType('down')->get();
        
        $this->assertCount(1, $upVotes);
        $this->assertCount(1, $downVotes);
        $this->assertEquals('up', $upVotes->first()->vote_type);
        $this->assertEquals('down', $downVotes->first()->vote_type);
    }

    public function test_vote_scope_by_user()
    {
        $user1 = $this->createUser();
        $user2 = $this->createUser();
        
        Vote::factory()->create(['user_id' => $user1->id]);
        Vote::factory()->create(['user_id' => $user2->id]);
        
        $user1Votes = Vote::byUser($user1->id)->get();
        
        $this->assertCount(1, $user1Votes);
        $this->assertEquals($user1->id, $user1Votes->first()->user_id);
    }

    public function test_vote_scope_by_feature_request()
    {
        $featureRequest1 = FeatureRequest::factory()->create();
        $featureRequest2 = FeatureRequest::factory()->create();
        
        Vote::factory()->create(['feature_request_id' => $featureRequest1->id]);
        Vote::factory()->create(['feature_request_id' => $featureRequest2->id]);
        
        $featureRequest1Votes = Vote::byFeatureRequest($featureRequest1->id)->get();
        
        $this->assertCount(1, $featureRequest1Votes);
        $this->assertEquals($featureRequest1->id, $featureRequest1Votes->first()->feature_request_id);
    }

    public function test_vote_scope_up_votes()
    {
        Vote::factory()->create(['vote_type' => 'up']);
        Vote::factory()->create(['vote_type' => 'down']);
        
        $upVotes = Vote::upVotes()->get();
        
        $this->assertCount(1, $upVotes);
        $this->assertEquals('up', $upVotes->first()->vote_type);
    }

    public function test_vote_scope_down_votes()
    {
        Vote::factory()->create(['vote_type' => 'up']);
        Vote::factory()->create(['vote_type' => 'down']);
        
        $downVotes = Vote::downVotes()->get();
        
        $this->assertCount(1, $downVotes);
        $this->assertEquals('down', $downVotes->first()->vote_type);
    }
}
