<?php

declare(strict_types=1);

namespace LaravelPlus\FeatureRequests\Contracts\Services;

use LaravelPlus\FeatureRequests\Models\Vote;
use Illuminate\Database\Eloquent\Collection;

interface VoteServiceInterface extends BaseServiceInterface
{
    /**
     * Vote on a feature request.
     */
    public function vote(int $featureRequestId, string $voteType, ?string $comment = null): Vote;

    /**
     * Remove vote from a feature request.
     */
    public function removeVote(int $featureRequestId): bool;

    /**
     * Get all votes for a feature request.
     */
    public function getVotes(int $featureRequestId): Collection;

    /**
     * Get up votes for a feature request.
     */
    public function getUpVotes(int $featureRequestId): Collection;

    /**
     * Get down votes for a feature request.
     */
    public function getDownVotes(int $featureRequestId): Collection;

    /**
     * Get votes by user.
     */
    public function getVotesByUser(int $userId): Collection;

    /**
     * Get up votes by user.
     */
    public function getUpVotesByUser(int $userId): Collection;

    /**
     * Get down votes by user.
     */
    public function getDownVotesByUser(int $userId): Collection;

    /**
     * Get total vote count for a feature request.
     */
    public function getVoteCount(int $featureRequestId): int;

    /**
     * Get up vote count for a feature request.
     */
    public function getUpVoteCount(int $featureRequestId): int;

    /**
     * Get down vote count for a feature request.
     */
    public function getDownVoteCount(int $featureRequestId): int;

    /**
     * Get vote statistics for a feature request.
     */
    public function getVoteStatistics(int $featureRequestId): array;

    /**
     * Check if user has voted on a feature request.
     */
    public function hasUserVoted(int $featureRequestId, ?int $userId = null): bool;

    /**
     * Get user's vote type for a feature request.
     */
    public function getUserVoteType(int $featureRequestId, ?int $userId = null): ?string;

    /**
     * Get most voted feature requests.
     */
    public function getMostVotedFeatureRequests(int $limit = 10): Collection;

    /**
     * Check if user can vote.
     */
    public function canVote(): bool;

    /**
     * Check if user can vote on a specific feature request.
     */
    public function canVoteOn(int $featureRequestId): bool;

    /**
     * Get user's voting statistics.
     */
    public function getUserVotingStatistics(int $userId): array;
}