<?php

namespace LaravelPlus\FeatureRequests\Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use LaravelPlus\FeatureRequests\Models\FeatureRequest;
use LaravelPlus\FeatureRequests\Models\Vote;
use LaravelPlus\FeatureRequests\Tests\TestCase;

class VoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_vote_can_be_created()
    {
        $user = \App\Models\User::factory()->create();
        $featureRequest = FeatureRequest::factory()->create();

        $vote = Vote::factory()->create([
            'feature_request_id' => $featureRequest->id,
            'user_id' => $user->id,
            'vote_type' => 'up',
        ]);

        $this->assertDatabaseHas('feature_request_votes', [
            'feature_request_id' => $featureRequest->id,
            'user_id' => $user->id,
            'vote_type' => 'up',
        ]);

        $this->assertEquals($featureRequest->id, $vote->feature_request_id);
        $this->assertEquals($user->id, $vote->user_id);
        $this->assertEquals('up', $vote->vote_type);
    }

    public function test_vote_belongs_to_user()
    {
        $user = \App\Models\User::factory()->create();
        $vote = Vote::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(\App\Models\User::class, $vote->user);
        $this->assertEquals($user->id, $vote->user->id);
    }

    public function test_vote_belongs_to_feature_request()
    {
        $featureRequest = FeatureRequest::factory()->create();
        $vote = Vote::factory()->create(['feature_request_id' => $featureRequest->id]);

        $this->assertInstanceOf(FeatureRequest::class, $vote->featureRequest);
        $this->assertEquals($featureRequest->id, $vote->featureRequest->id);
    }

    public function test_vote_type_must_be_valid()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Vote::factory()->create(['vote_type' => 'invalid']);
    }

    public function test_vote_can_be_soft_deleted()
    {
        $vote = Vote::factory()->create();
        $vote->delete();

        $this->assertSoftDeleted('feature_request_votes', [
            'id' => $vote->id
        ]);
    }

    public function test_vote_scope_up()
    {
        Vote::factory()->create(['vote_type' => 'up']);
        Vote::factory()->create(['vote_type' => 'down']);

        $upVotes = Vote::up()->get();
        $this->assertCount(1, $upVotes);
        $this->assertEquals('up', $upVotes->first()->vote_type);
    }

    public function test_vote_scope_down()
    {
        Vote::factory()->create(['vote_type' => 'up']);
        Vote::factory()->create(['vote_type' => 'down']);

        $downVotes = Vote::down()->get();
        $this->assertCount(1, $downVotes);
        $this->assertEquals('down', $downVotes->first()->vote_type);
    }

    public function test_vote_scope_by_user()
    {
        $user1 = \App\Models\User::factory()->create();
        $user2 = \App\Models\User::factory()->create();

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
}