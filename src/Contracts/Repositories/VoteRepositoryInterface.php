<?php

declare(strict_types=1);

namespace LaravelPlus\FeatureRequests\Contracts\Repositories;

use LaravelPlus\FeatureRequests\Models\Vote;
use Illuminate\Database\Eloquent\Collection;

interface VoteRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find a vote by feature request and user.
     */
    public function findByFeatureRequestAndUser(int $featureRequestId, int $userId): ?Vote;

    /**
     * Delete vote by feature request and user.
     */
    public function deleteByFeatureRequestAndUser(int $featureRequestId, int $userId): bool;

    /**
     * Get votes by feature request.
     */
    public function getByFeatureRequest(int $featureRequestId): Collection;

    /**
     * Get up votes by feature request.
     */
    public function getUpVotesByFeatureRequest(int $featureRequestId): Collection;

    /**
     * Get down votes by feature request.
     */
    public function getDownVotesByFeatureRequest(int $featureRequestId): Collection;

    /**
     * Get votes by user.
     */
    public function getByUser(int $userId): Collection;

    /**
     * Get up votes by user.
     */
    public function getUpVotesByUser(int $userId): Collection;

    /**
     * Get down votes by user.
     */
    public function getDownVotesByUser(int $userId): Collection;

    /**
     * Get vote count for feature request.
     */
    public function getVoteCount(int $featureRequestId): int;

    /**
     * Get up vote count for feature request.
     */
    public function getUpVoteCount(int $featureRequestId): int;

    /**
     * Get down vote count for feature request.
     */
    public function getDownVoteCount(int $featureRequestId): int;

    /**
     * Get vote statistics for feature request.
     */
    public function getVoteStatistics(int $featureRequestId): array;

    /**
     * Check if user has voted on feature request.
     */
    public function hasUserVoted(int $featureRequestId, int $userId): bool;

    /**
     * Get user's vote type for feature request.
     */
    public function getUserVoteType(int $featureRequestId, int $userId): ?string;

    /**
     * Get most voted feature requests.
     */
    public function getMostVotedFeatureRequests(int $limit = 10): Collection;
}