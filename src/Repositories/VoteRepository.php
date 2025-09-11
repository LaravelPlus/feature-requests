<?php

declare(strict_types=1);

namespace LaravelPlus\FeatureRequests\Repositories;

use LaravelPlus\FeatureRequests\Models\Vote;
use Illuminate\Database\Eloquent\Collection;

final class VoteRepository
{
    protected Vote $model;

    public function __construct(Vote $model)
    {
        $this->model = $model;
    }

    /**
     * Get all votes.
     */
    public function all(): Collection
    {
        return $this->model->with(['user', 'featureRequest'])->get();
    }

    /**
     * Find a vote by ID.
     */
    public function find(int $id): ?Vote
    {
        return $this->model->with(['user', 'featureRequest'])->find($id);
    }

    /**
     * Find a vote by feature request and user.
     */
    public function findByFeatureRequestAndUser(int $featureRequestId, int $userId): ?Vote
    {
        return $this->model->where('feature_request_id', $featureRequestId)
                          ->where('user_id', $userId)
                          ->first();
    }

    /**
     * Create a new vote.
     */
    public function create(array $data): Vote
    {
        return $this->model->create($data);
    }

    /**
     * Update a vote.
     */
    public function update(int $id, array $data): bool
    {
        $vote = $this->find($id);
        
        if (!$vote) {
            return false;
        }

        return $vote->update($data);
    }

    /**
     * Delete a vote.
     */
    public function delete(int $id): bool
    {
        $vote = $this->find($id);
        
        if (!$vote) {
            return false;
        }

        return $vote->delete();
    }

    /**
     * Delete a vote by feature request and user.
     */
    public function deleteByFeatureRequestAndUser(int $featureRequestId, int $userId): bool
    {
        return $this->model->where('feature_request_id', $featureRequestId)
                          ->where('user_id', $userId)
                          ->delete() > 0;
    }

    /**
     * Get votes for a feature request.
     */
    public function getByFeatureRequest(int $featureRequestId): Collection
    {
        return $this->model->where('feature_request_id', $featureRequestId)
                          ->with(['user'])
                          ->get();
    }

    /**
     * Get up votes for a feature request.
     */
    public function getUpVotesByFeatureRequest(int $featureRequestId): Collection
    {
        return $this->model->where('feature_request_id', $featureRequestId)
                          ->upVotes()
                          ->with(['user'])
                          ->get();
    }

    /**
     * Get down votes for a feature request.
     */
    public function getDownVotesByFeatureRequest(int $featureRequestId): Collection
    {
        return $this->model->where('feature_request_id', $featureRequestId)
                          ->downVotes()
                          ->with(['user'])
                          ->get();
    }

    /**
     * Get votes by user.
     */
    public function getByUser(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)
                          ->with(['featureRequest'])
                          ->get();
    }

    /**
     * Get up votes by user.
     */
    public function getUpVotesByUser(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)
                          ->upVotes()
                          ->with(['featureRequest'])
                          ->get();
    }

    /**
     * Get down votes by user.
     */
    public function getDownVotesByUser(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)
                          ->downVotes()
                          ->with(['featureRequest'])
                          ->get();
    }

    /**
     * Get vote count for a feature request.
     */
    public function getVoteCount(int $featureRequestId): int
    {
        return $this->model->where('feature_request_id', $featureRequestId)->count();
    }

    /**
     * Get up vote count for a feature request.
     */
    public function getUpVoteCount(int $featureRequestId): int
    {
        return $this->model->where('feature_request_id', $featureRequestId)
                          ->upVotes()
                          ->count();
    }

    /**
     * Get down vote count for a feature request.
     */
    public function getDownVoteCount(int $featureRequestId): int
    {
        return $this->model->where('feature_request_id', $featureRequestId)
                          ->downVotes()
                          ->count();
    }

    /**
     * Get vote statistics for a feature request.
     */
    public function getVoteStatistics(int $featureRequestId): array
    {
        $total = $this->getVoteCount($featureRequestId);
        $upVotes = $this->getUpVoteCount($featureRequestId);
        $downVotes = $this->getDownVoteCount($featureRequestId);

        return [
            'total_votes' => $total,
            'total' => $total,
            'up_votes' => $upVotes,
            'down_votes' => $downVotes,
            'net_votes' => $upVotes - $downVotes,
            'approval_rate' => $total > 0 ? round(($upVotes / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Check if user has voted on a feature request.
     */
    public function hasUserVoted(int $featureRequestId, int $userId): bool
    {
        return $this->model->where('feature_request_id', $featureRequestId)
                          ->where('user_id', $userId)
                          ->exists();
    }

    /**
     * Get user's vote type for a feature request.
     */
    public function getUserVoteType(int $featureRequestId, int $userId): ?string
    {
        $vote = $this->findByFeatureRequestAndUser($featureRequestId, $userId);
        
        return $vote ? $vote->vote_type : null;
    }

    /**
     * Get most voted feature requests.
     */
    public function getMostVotedFeatureRequests(int $limit = 10): Collection
    {
        return $this->model->selectRaw('feature_request_id, COUNT(*) as vote_count')
                          ->groupBy('feature_request_id')
                          ->orderBy('vote_count', 'desc')
                          ->limit($limit)
                          ->with(['featureRequest.user', 'featureRequest.category'])
                          ->get();
    }
}
