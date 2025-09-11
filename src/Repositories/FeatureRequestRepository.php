<?php

declare(strict_types=1);

namespace LaravelPlus\FeatureRequests\Repositories;

use LaravelPlus\FeatureRequests\Models\FeatureRequest;
use LaravelPlus\FeatureRequests\Contracts\Repositories\FeatureRequestRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class FeatureRequestRepository implements FeatureRequestRepositoryInterface
{
    protected FeatureRequest $model;

    public function __construct(FeatureRequest $model)
    {
        $this->model = $model;
    }

    /**
     * Get all feature requests with pagination.
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        // Apply filters
        if (isset($filters['status'])) {
            $query->status($filters['status']);
        }

        if (isset($filters['category_id'])) {
            $query->category($filters['category_id']);
        }

        if (isset($filters['search'])) {
            $query->search($filters['search']);
        }

        if (isset($filters['is_public'])) {
            $query->where('is_public', $filters['is_public']);
        }

        if (isset($filters['is_featured'])) {
            $query->where('is_featured', $filters['is_featured']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        if (isset($filters['exclude_completed']) && $filters['exclude_completed']) {
            $query->where('status', '!=', 'completed');
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';

        switch ($sortBy) {
            case 'votes':
                $query->orderByVotes($sortDirection);
                break;
            case 'newest':
                $query->orderByNewest();
                break;
            case 'title':
                $query->orderBy('title', $sortDirection);
                break;
            case 'status':
                $query->orderBy('status', $sortDirection);
                break;
            default:
                $query->orderBy($sortBy, $sortDirection);
        }

        return $query->with(['user', 'category', 'assignedTo'])
                    ->paginate($perPage);
    }

    /**
     * Get all feature requests.
     */
    public function all(array $filters = []): Collection
    {
        $query = $this->model->newQuery();

        // Apply filters
        if (isset($filters['status'])) {
            $query->status($filters['status']);
        }

        if (isset($filters['category_id'])) {
            $query->category($filters['category_id']);
        }

        if (isset($filters['search'])) {
            $query->search($filters['search']);
        }

        if (isset($filters['is_public'])) {
            $query->where('is_public', $filters['is_public']);
        }

        if (isset($filters['is_featured'])) {
            $query->where('is_featured', $filters['is_featured']);
        }

        return $query->with(['user', 'category', 'assignedTo'])->get();
    }

    /**
     * Find a feature request by ID.
     */
    public function find(int $id): ?FeatureRequest
    {
        return $this->model->with(['user', 'category', 'assignedTo', 'votes.user', 'comments.user'])
                          ->find($id);
    }

    /**
     * Find a feature request by slug.
     */
    public function findBySlug(string $slug): ?FeatureRequest
    {
        return $this->model->with(['user', 'category', 'assignedTo', 'votes.user', 'comments.user'])
                          ->where('slug', $slug)
                          ->first();
    }

    /**
     * Find a feature request by UUID.
     */
    public function findByUuid(string $uuid): ?FeatureRequest
    {
        return $this->model->with(['user', 'category', 'assignedTo', 'votes.user', 'comments.user'])
                          ->where('uuid', $uuid)
                          ->first();
    }

    /**
     * Create a new feature request.
     */
    public function create(array $data): FeatureRequest
    {
        return $this->model->create($data);
    }

    /**
     * Update a feature request.
     */
    public function update(int $id, array $data): bool
    {
        $featureRequest = $this->find($id);
        
        if (!$featureRequest) {
            return false;
        }

        return $featureRequest->update($data);
    }

    /**
     * Delete a feature request.
     */
    public function delete(int $id): bool
    {
        $featureRequest = $this->find($id);
        
        if (!$featureRequest) {
            return false;
        }

        return $featureRequest->delete();
    }

    /**
     * Get feature requests by status.
     */
    public function getByStatus(string $status): Collection
    {
        return $this->model->status($status)
                          ->with(['user', 'category', 'assignedTo'])
                          ->get();
    }

    /**
     * Get feature requests by category.
     */
    public function getByCategory(int $categoryId): Collection
    {
        return $this->model->category($categoryId)
                          ->with(['user', 'category', 'assignedTo'])
                          ->get();
    }

    /**
     * Get feature requests by user.
     */
    public function getByUser(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)
                          ->with(['user', 'category', 'assignedTo'])
                          ->get();
    }

    /**
     * Get assigned feature requests.
     */
    public function getAssignedTo(int $userId): Collection
    {
        return $this->model->where('assigned_to', $userId)
                          ->with(['user', 'category', 'assignedTo'])
                          ->get();
    }

    /**
     * Get featured feature requests.
     */
    public function getFeatured(): Collection
    {
        return $this->model->featured()
                          ->with(['user', 'category', 'assignedTo'])
                          ->get();
    }

    /**
     * Get most voted feature requests.
     */
    public function getMostVoted(int $limit = 10): Collection
    {
        return $this->model->orderByVotes('desc')
                          ->with(['user', 'category', 'assignedTo'])
                          ->limit($limit)
                          ->get();
    }

    /**
     * Get recent feature requests.
     */
    public function getRecent(int $limit = 10): Collection
    {
        return $this->model->orderByNewest()
                          ->with(['user', 'category', 'assignedTo'])
                          ->limit($limit)
                          ->get();
    }

    /**
     * Search feature requests.
     */
    public function search(string $search, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->search($search)
                          ->with(['user', 'category', 'assignedTo'])
                          ->paginate($perPage);
    }

    /**
     * Get statistics.
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->model->count(),
            'pending' => $this->model->status('pending')->count(),
            'under_review' => $this->model->status('under_review')->count(),
            'planned' => $this->model->status('planned')->count(),
            'in_progress' => $this->model->status('in_progress')->count(),
            'completed' => $this->model->status('completed')->count(),
            'rejected' => $this->model->status('rejected')->count(),
            'featured' => $this->model->featured()->count(),
            'public' => $this->model->public()->count(),
        ];
    }

    /**
     * Get public statistics (only for public feature requests).
     */
    public function getPublicStatistics(): array
    {
        $publicQuery = $this->model->where('is_public', true);
        
        return [
            'total' => $publicQuery->count(),
            'pending' => $publicQuery->clone()->status('pending')->count(),
            'in_progress' => $publicQuery->clone()->status('in_progress')->count(),
            'completed' => $publicQuery->clone()->status('completed')->count(),
            'rejected' => $publicQuery->clone()->status('rejected')->count(),
            'featured' => $publicQuery->clone()->featured()->count(),
            'total_votes' => $publicQuery->clone()->sum('vote_count'),
            'total_comments' => $publicQuery->clone()->sum('comment_count'),
        ];
    }

    /**
     * Get feature requests grouped by status for roadmap.
     */
    public function getForRoadmap(array $filters = []): array
    {
        $query = $this->model->newQuery();

        // Apply filters
        if (isset($filters['category_id'])) {
            $query->category($filters['category_id']);
        }

        if (isset($filters['search'])) {
            $query->search($filters['search']);
        }

        if (isset($filters['is_public'])) {
            $query->where('is_public', $filters['is_public']);
        }

        // Get feature requests grouped by status
        $featureRequests = $query->with(['user', 'category'])
                                ->orderBy('vote_count', 'desc')
                                ->orderBy('created_at', 'desc')
                                ->get()
                                ->groupBy('status');

        // Ensure all statuses are present (excluding rejected and completed from roadmap)
        $statuses = ['pending', 'under_review', 'in_progress'];
        $result = [];
        
        foreach ($statuses as $status) {
            $result[$status] = $featureRequests->get($status, collect());
        }

        return $result;
    }

    /**
     * Get completed feature requests for changelog.
     */
    public function getForChangelog(array $filters = []): Collection
    {
        $query = $this->model->newQuery();

        // Apply filters
        if (isset($filters['category_id'])) {
            $query->category($filters['category_id']);
        }

        if (isset($filters['search'])) {
            $query->search($filters['search']);
        }

        if (isset($filters['is_public'])) {
            $query->where('is_public', $filters['is_public']);
        }

        // Only get completed feature requests, ordered by completion date (updated_at)
        return $query->where('status', 'completed')
                    ->with(['user', 'category'])
                    ->orderBy('updated_at', 'desc')
                    ->get();
    }

    /**
     * Get feature requests that need attention.
     */
    public function getNeedingAttention(): Collection
    {
        return $this->model->whereIn('status', ['pending', 'under_review'])
                          ->where(function ($query) {
                              $query->whereNull('assigned_to')
                                    ->orWhere('due_date', '<', now());
                          })
                          ->with(['user', 'category', 'assignedTo'])
                          ->get();
    }
}
