<?php

namespace LaravelPlus\FeatureRequests\Services;

use LaravelPlus\FeatureRequests\Repositories\VoteRepository;
use LaravelPlus\FeatureRequests\Repositories\FeatureRequestRepository;
use LaravelPlus\FeatureRequests\Models\Vote;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class VoteService
{
    protected VoteRepository $voteRepository;
    protected FeatureRequestRepository $featureRequestRepository;

    public function __construct(
        VoteRepository $voteRepository,
        FeatureRequestRepository $featureRequestRepository
    ) {
        $this->voteRepository = $voteRepository;
        $this->featureRequestRepository = $featureRequestRepository;
    }

    /**
     * Vote on a feature request.
     */
    public function vote(int $featureRequestId, string $voteType, ?string $comment = null): Vote
    {
        $userId = Auth::id();
        
        if (!$userId) {
            throw new \Exception('User must be authenticated to vote.');
        }

        if (!config('feature-requests.voting.enabled', true)) {
            throw new \Exception('Voting is disabled.');
        }

        if (!config('feature-requests.voting.require_authentication', true) && !$userId) {
            throw new \Exception('Authentication is required to vote.');
        }

        // Check if user has already voted
        $existingVote = $this->voteRepository->findByFeatureRequestAndUser($featureRequestId, $userId);

        if ($existingVote) {
            if (!config('feature-requests.voting.allow_vote_changes', true)) {
                throw new \Exception('You have already voted on this feature request.');
            }

            // Update existing vote
            $existingVote->update([
                'vote_type' => $voteType,
                'comment' => $comment,
            ]);

            $this->updateVoteCount($featureRequestId);
            $this->clearCache();
            
            return $existingVote;
        }

        // Check vote limits
        if (config('feature-requests.voting.max_votes_per_user')) {
            $userVoteCount = $this->voteRepository->getByUser($userId)->count();
            
            if ($userVoteCount >= config('feature-requests.voting.max_votes_per_user')) {
                throw new \Exception('You have reached the maximum number of votes allowed.');
            }
        }

        // Create new vote
        $vote = $this->voteRepository->create([
            'feature_request_id' => $featureRequestId,
            'user_id' => $userId,
            'vote_type' => $voteType,
            'comment' => $comment,
        ]);

        $this->updateVoteCount($featureRequestId);
        $this->clearCache();

        return $vote;
    }

    /**
     * Remove vote from a feature request.
     */
    public function removeVote(int $featureRequestId): bool
    {
        $userId = Auth::id();
        
        if (!$userId) {
            throw new \Exception('User must be authenticated to remove vote.');
        }

        $result = $this->voteRepository->deleteByFeatureRequestAndUser($featureRequestId, $userId);

        if ($result) {
            $this->updateVoteCount($featureRequestId);
            $this->clearCache();
        }

        return $result;
    }

    /**
     * Get votes for a feature request.
     */
    public function getVotes(int $featureRequestId)
    {
        return $this->voteRepository->getByFeatureRequest($featureRequestId);
    }

    /**
     * Get up votes for a feature request.
     */
    public function getUpVotes(int $featureRequestId)
    {
        return $this->voteRepository->getUpVotesByFeatureRequest($featureRequestId);
    }

    /**
     * Get down votes for a feature request.
     */
    public function getDownVotes(int $featureRequestId)
    {
        return $this->voteRepository->getDownVotesByFeatureRequest($featureRequestId);
    }

    /**
     * Get votes by user.
     */
    public function getVotesByUser(int $userId)
    {
        return $this->voteRepository->getByUser($userId);
    }

    /**
     * Get up votes by user.
     */
    public function getUpVotesByUser(int $userId)
    {
        return $this->voteRepository->getUpVotesByUser($userId);
    }

    /**
     * Get down votes by user.
     */
    public function getDownVotesByUser(int $userId)
    {
        return $this->voteRepository->getDownVotesByUser($userId);
    }

    /**
     * Get vote count for a feature request.
     */
    public function getVoteCount(int $featureRequestId): int
    {
        return $this->voteRepository->getVoteCount($featureRequestId);
    }

    /**
     * Get up vote count for a feature request.
     */
    public function getUpVoteCount(int $featureRequestId): int
    {
        return $this->voteRepository->getUpVoteCount($featureRequestId);
    }

    /**
     * Get down vote count for a feature request.
     */
    public function getDownVoteCount(int $featureRequestId): int
    {
        return $this->voteRepository->getDownVoteCount($featureRequestId);
    }

    /**
     * Get vote statistics for a feature request.
     */
    public function getVoteStatistics(int $featureRequestId): array
    {
        return $this->voteRepository->getVoteStatistics($featureRequestId);
    }

    /**
     * Check if user has voted on a feature request.
     */
    public function hasUserVoted(int $featureRequestId, ?int $userId = null): bool
    {
        $userId = $userId ?? Auth::id();
        
        if (!$userId) {
            return false;
        }

        return $this->voteRepository->hasUserVoted($featureRequestId, $userId);
    }

    /**
     * Get user's vote type for a feature request.
     */
    public function getUserVoteType(int $featureRequestId, ?int $userId = null): ?string
    {
        $userId = $userId ?? Auth::id();
        
        if (!$userId) {
            return null;
        }

        return $this->voteRepository->getUserVoteType($featureRequestId, $userId);
    }

    /**
     * Get most voted feature requests.
     */
    public function getMostVotedFeatureRequests(int $limit = 10)
    {
        return $this->voteRepository->getMostVotedFeatureRequests($limit);
    }

    /**
     * Update vote count for a feature request.
     */
    protected function updateVoteCount(int $featureRequestId): void
    {
        $featureRequest = $this->featureRequestRepository->find($featureRequestId);
        
        if ($featureRequest) {
            $featureRequest->updateVoteCount();
        }
    }

    /**
     * Clear cache.
     */
    protected function clearCache(): void
    {
        if (config('feature-requests.cache.enabled', true)) {
            Cache::tags(['feature-requests'])->flush();
        }
    }

    /**
     * Check if user can vote.
     */
    public function canVote(): bool
    {
        if (!config('feature-requests.voting.enabled', true)) {
            return false;
        }

        if (config('feature-requests.voting.require_authentication', true) && !Auth::check()) {
            return false;
        }

        return Auth::user()->can(config('feature-requests.permissions.vote_feature_request', 'vote on feature requests'));
    }

    /**
     * Check if user can vote on a specific feature request.
     */
    public function canVoteOn(int $featureRequestId): bool
    {
        if (!$this->canVote()) {
            return false;
        }

        $featureRequest = $this->featureRequestRepository->find($featureRequestId);
        
        if (!$featureRequest) {
            return false;
        }

        return $featureRequest->canBeVotedOn();
    }

    /**
     * Get user's voting statistics.
     */
    public function getUserVotingStatistics(int $userId): array
    {
        $upVotes = $this->getUpVotesByUser($userId)->count();
        $downVotes = $this->getDownVotesByUser($userId)->count();
        $totalVotes = $upVotes + $downVotes;

        return [
            'total_votes' => $totalVotes,
            'up_votes' => $upVotes,
            'down_votes' => $downVotes,
            'net_votes' => $upVotes - $downVotes,
            'approval_rate' => $totalVotes > 0 ? round(($upVotes / $totalVotes) * 100, 2) : 0,
        ];
    }
}
