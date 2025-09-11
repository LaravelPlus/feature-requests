<?php

declare(strict_types=1);

namespace LaravelPlus\FeatureRequests\Contracts\Repositories;

use LaravelPlus\FeatureRequests\Models\Comment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CommentRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get comments by feature request.
     */
    public function getByFeatureRequest(int $featureRequestId): Collection;

    /**
     * Get top-level comments by feature request.
     */
    public function getTopLevelByFeatureRequest(int $featureRequestId): Collection;

    /**
     * Get replies to a comment.
     */
    public function getReplies(int $parentId): Collection;

    /**
     * Get comments by user.
     */
    public function getByUser(int $userId): Collection;

    /**
     * Get approved comments.
     */
    public function getApproved(): Collection;

    /**
     * Get pending comments.
     */
    public function getPending(): Collection;

    /**
     * Get pinned comments.
     */
    public function getPinned(): Collection;

    /**
     * Get comment count by feature request.
     */
    public function getCountByFeatureRequest(int $featureRequestId): int;

    /**
     * Get comment count by user.
     */
    public function getCountByUser(int $userId): int;

    /**
     * Approve a comment.
     */
    public function approve(int $id): bool;

    /**
     * Pin a comment.
     */
    public function pin(int $id): bool;

    /**
     * Unpin a comment.
     */
    public function unpin(int $id): bool;

    /**
     * Get recent comments.
     */
    public function getRecent(int $limit = 10): Collection;

    /**
     * Search comments.
     */
    public function search(string $search, int $perPage = 15): LengthAwarePaginator;
}