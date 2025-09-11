<?php

declare(strict_types=1);

namespace LaravelPlus\FeatureRequests\Contracts\Repositories;

use LaravelPlus\FeatureRequests\Models\FeatureRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface FeatureRequestRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find a feature request by slug.
     */
    public function findBySlug(string $slug): ?FeatureRequest;

    /**
     * Find a feature request by UUID.
     */
    public function findByUuid(string $uuid): ?FeatureRequest;

    /**
     * Get feature requests by status.
     */
    public function getByStatus(string $status): Collection;

    /**
     * Get feature requests by category.
     */
    public function getByCategory(int $categoryId): Collection;

    /**
     * Get feature requests by user.
     */
    public function getByUser(int $userId): Collection;

    /**
     * Get feature requests assigned to user.
     */
    public function getAssignedTo(int $userId): Collection;

    /**
     * Get featured feature requests.
     */
    public function getFeatured(): Collection;

    /**
     * Get most voted feature requests.
     */
    public function getMostVoted(int $limit = 10): Collection;

    /**
     * Get recent feature requests.
     */
    public function getRecent(int $limit = 10): Collection;

    /**
     * Search feature requests.
     */
    public function search(string $search, int $perPage = 15): LengthAwarePaginator;

    /**
     * Get feature request statistics.
     */
    public function getStatistics(): array;

    /**
     * Get public feature request statistics.
     */
    public function getPublicStatistics(): array;

    /**
     * Get feature requests for roadmap.
     */
    public function getForRoadmap(array $filters = []): array;

    /**
     * Get feature requests for changelog.
     */
    public function getForChangelog(array $filters = []): Collection;

    /**
     * Get feature requests needing attention.
     */
    public function getNeedingAttention(): Collection;
}