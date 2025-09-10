<?php

namespace LaravelPlus\FeatureRequests\Tests\Unit\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use LaravelPlus\FeatureRequests\Models\FeatureRequest;
use LaravelPlus\FeatureRequests\Models\Vote;
use LaravelPlus\FeatureRequests\Services\VoteService;
use LaravelPlus\FeatureRequests\Tests\TestCase;

class VoteServiceTest extends TestCase
{
    use RefreshDatabase;

    protected VoteService $voteService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->voteService = app(VoteService::class);
    }

    public function test_user_can_vote_up()
    {
        $user = \App\Models\User::factory()->create();
        $featureRequest = FeatureRequest::factory()->create();

        $result = $this->voteService->vote($featureRequest->id, $user->id, 'up');

        $this->assertTrue($result);
        $this->assertDatabaseHas('feature_request_votes', [
            'feature_request_id' => $featureRequest->id,
            'user_id' => $user->id,
            'vote_type' => 'up',
        ]);
    }

    public function test_user_can_vote_down()
    {
        $user = \App\Models\User::factory()->create();
        $featureRequest = FeatureRequest::factory()->create();

        $result = $this->voteService->vote($featureRequest->id, $user->id, 'down');

        $this->assertTrue($result);
        $this->assertDatabaseHas('feature_request_votes', [
            'feature_request_id' => $featureRequest->id,
            'user_id' => $user->id,
            'vote_type' => 'down',
        ]);
    }

    public function test_user_can_change_vote()
    {
        $user = \App\Models\User::factory()->create();
        $featureRequest = FeatureRequest::factory()->create();

        // First vote up
        $this->voteService->vote($featureRequest->id, $user->id, 'up');
        
        // Change to down vote
        $result = $this->voteService->vote($featureRequest->id, $user->id, 'down');

        $this->assertTrue($result);
        $this->assertDatabaseHas('feature_request_votes', [
            'feature_request_id' => $featureRequest->id,
            'user_id' => $user->id,
            'vote_type' => 'down',
        ]);

        // Should only have one vote record
        $this->assertDatabaseCount('feature_request_votes', 1);
    }

    public function test_user_can_remove_vote()
    {
        $user = \App\Models\User::factory()->create();
        $featureRequest = FeatureRequest::factory()->create();

        // Create a vote
        $this->voteService->vote($featureRequest->id, $user->id, 'up');
        
        // Remove the vote
        $result = $this->voteService->removeVote($featureRequest->id, $user->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('feature_request_votes', [
            'feature_request_id' => $featureRequest->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_has_user_voted_returns_true_when_user_has_voted()
    {
        $user = \App\Models\User::factory()->create();
        $featureRequest = FeatureRequest::factory()->create();

        $this->voteService->vote($featureRequest->id, $user->id, 'up');

        $this->assertTrue($this->voteService->hasUserVoted($featureRequest->id, $user->id));
    }

    public function test_has_user_voted_returns_false_when_user_has_not_voted()
    {
        $user = \App\Models\User::factory()->create();
        $featureRequest = FeatureRequest::factory()->create();

        $this->assertFalse($this->voteService->hasUserVoted($featureRequest->id, $user->id));
    }

    public function test_get_user_vote_type_returns_correct_type()
    {
        $user = \App\Models\User::factory()->create();
        $featureRequest = FeatureRequest::factory()->create();

        $this->voteService->vote($featureRequest->id, $user->id, 'up');
        $this->assertEquals('up', $this->voteService->getUserVoteType($featureRequest->id, $user->id));

        $this->voteService->vote($featureRequest->id, $user->id, 'down');
        $this->assertEquals('down', $this->voteService->getUserVoteType($featureRequest->id, $user->id));
    }

    public function test_get_user_vote_type_returns_null_when_no_vote()
    {
        $user = \App\Models\User::factory()->create();
        $featureRequest = FeatureRequest::factory()->create();

        $this->assertNull($this->voteService->getUserVoteType($featureRequest->id, $user->id));
    }

    public function test_get_vote_statistics()
    {
        $featureRequest = FeatureRequest::factory()->create();
        $user1 = \App\Models\User::factory()->create();
        $user2 = \App\Models\User::factory()->create();
        $user3 = \App\Models\User::factory()->create();

        $this->voteService->vote($featureRequest->id, $user1->id, 'up');
        $this->voteService->vote($featureRequest->id, $user2->id, 'up');
        $this->voteService->vote($featureRequest->id, $user3->id, 'down');

        $stats = $this->voteService->getVoteStatistics($featureRequest->id);

        $this->assertEquals(3, $stats['total']);
        $this->assertEquals(2, $stats['up_votes']);
        $this->assertEquals(1, $stats['down_votes']);
        $this->assertEquals(1, $stats['net_votes']);
        $this->assertEquals(66.67, round($stats['approval_rate'], 2));
    }

    public function test_vote_updates_feature_request_vote_count()
    {
        $user = \App\Models\User::factory()->create();
        $featureRequest = FeatureRequest::factory()->create([
            'up_votes' => 0,
            'down_votes' => 0,
            'vote_count' => 0,
        ]);

        $this->voteService->vote($featureRequest->id, $user->id, 'up');

        $featureRequest->refresh();
        $this->assertEquals(1, $featureRequest->up_votes);
        $this->assertEquals(0, $featureRequest->down_votes);
        $this->assertEquals(1, $featureRequest->vote_count);
    }

    public function test_vote_clears_cache()
    {
        Cache::shouldReceive('tags')
            ->with(['feature-requests'])
            ->andReturnSelf();
        
        Cache::shouldReceive('flush')
            ->once();

        $user = \App\Models\User::factory()->create();
        $featureRequest = FeatureRequest::factory()->create();

        $this->voteService->vote($featureRequest->id, $user->id, 'up');
    }

    public function test_remove_vote_clears_cache()
    {
        Cache::shouldReceive('tags')
            ->with(['feature-requests'])
            ->andReturnSelf();
        
        Cache::shouldReceive('flush')
            ->once();

        $user = \App\Models\User::factory()->create();
        $featureRequest = FeatureRequest::factory()->create();

        $this->voteService->vote($featureRequest->id, $user->id, 'up');
        $this->voteService->removeVote($featureRequest->id, $user->id);
    }
}
