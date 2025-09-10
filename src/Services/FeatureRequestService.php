<?php

namespace LaravelPlus\FeatureRequests\Services;

use LaravelPlus\FeatureRequests\Repositories\FeatureRequestRepository;
use LaravelPlus\FeatureRequests\Repositories\VoteRepository;
use LaravelPlus\FeatureRequests\Repositories\CommentRepository;
use LaravelPlus\FeatureRequests\Models\FeatureRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class FeatureRequestService
{
    protected FeatureRequestRepository $featureRequestRepository;
    protected VoteRepository $voteRepository;
    protected CommentRepository $commentRepository;

    public function __construct(
        FeatureRequestRepository $featureRequestRepository,
        VoteRepository $voteRepository,
        CommentRepository $commentRepository
    ) {
        $this->featureRequestRepository = $featureRequestRepository;
        $this->voteRepository = $voteRepository;
        $this->commentRepository = $commentRepository;
    }

    /**
     * Get all feature requests with pagination.
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $cacheKey = 'feature_requests_' . md5(serialize($filters) . $perPage);
        
        if (config('feature-requests.cache.enabled', true)) {
            return Cache::tags(['feature-requests'])->remember($cacheKey, config('feature-requests.cache.ttl', 3600), function () use ($perPage, $filters) {
                return $this->featureRequestRepository->paginate($perPage, $filters);
            });
        }

        return $this->featureRequestRepository->paginate($perPage, $filters);
    }

    /**
     * Get all feature requests.
     */
    public function all(array $filters = []): Collection
    {
        return $this->featureRequestRepository->all($filters);
    }

    /**
     * Find a feature request by ID.
     */
    public function find(int $id): ?FeatureRequest
    {
        return $this->featureRequestRepository->find($id);
    }

    /**
     * Find a feature request by slug.
     */
    public function findBySlug(string $slug): ?FeatureRequest
    {
        return $this->featureRequestRepository->findBySlug($slug);
    }

    /**
     * Create a new feature request.
     */
    public function create(array $data): FeatureRequest
    {
        // Set default values
        $data['user_id'] = $data['user_id'] ?? Auth::id();
        $data['status'] = $data['status'] ?? 'pending';
        $data['is_public'] = $data['is_public'] ?? true;
        $data['is_featured'] = $data['is_featured'] ?? false;
        $data['vote_count'] = 0;
        $data['up_votes'] = 0;
        $data['down_votes'] = 0;
        $data['comment_count'] = 0;
        $data['view_count'] = 0;

        $featureRequest = $this->featureRequestRepository->create($data);

        // Clear cache
        $this->clearCache();

        return $featureRequest;
    }

    /**
     * Update a feature request.
     */
    public function update(int $id, array $data): bool
    {
        $result = $this->featureRequestRepository->update($id, $data);

        if ($result) {
            // Clear cache
            $this->clearCache();
        }

        return $result;
    }

    /**
     * Delete a feature request.
     */
    public function delete(int $id): bool
    {
        $result = $this->featureRequestRepository->delete($id);

        if ($result) {
            // Clear cache
            $this->clearCache();
        }

        return $result;
    }

    /**
     * Get feature requests by status.
     */
    public function getByStatus(string $status): Collection
    {
        return $this->featureRequestRepository->getByStatus($status);
    }

    /**
     * Get feature requests by category.
     */
    public function getByCategory(int $categoryId): Collection
    {
        return $this->featureRequestRepository->getByCategory($categoryId);
    }

    /**
     * Get feature requests by user.
     */
    public function getByUser(int $userId): Collection
    {
        return $this->featureRequestRepository->getByUser($userId);
    }

    /**
     * Get assigned feature requests.
     */
    public function getAssignedTo(int $userId): Collection
    {
        return $this->featureRequestRepository->getAssignedTo($userId);
    }

    /**
     * Get featured feature requests.
     */
    public function getFeatured(): Collection
    {
        $cacheKey = 'featured_feature_requests';
        
        if (config('feature-requests.cache.enabled', true)) {
            return Cache::tags(['feature-requests'])->remember($cacheKey, config('feature-requests.cache.ttl', 3600), function () {
                return $this->featureRequestRepository->getFeatured();
            });
        }

        return $this->featureRequestRepository->getFeatured();
    }

    /**
     * Get most voted feature requests.
     */
    public function getMostVoted(int $limit = 10): Collection
    {
        $cacheKey = "most_voted_feature_requests_{$limit}";
        
        if (config('feature-requests.cache.enabled', true)) {
            return Cache::tags(['feature-requests'])->remember($cacheKey, config('feature-requests.cache.ttl', 3600), function () use ($limit) {
                return $this->featureRequestRepository->getMostVoted($limit);
            });
        }

        return $this->featureRequestRepository->getMostVoted($limit);
    }

    /**
     * Get recent feature requests.
     */
    public function getRecent(int $limit = 10): Collection
    {
        $cacheKey = "recent_feature_requests_{$limit}";
        
        if (config('feature-requests.cache.enabled', true)) {
            return Cache::tags(['feature-requests'])->remember($cacheKey, config('feature-requests.cache.ttl', 3600), function () use ($limit) {
                return $this->featureRequestRepository->getRecent($limit);
            });
        }

        return $this->featureRequestRepository->getRecent($limit);
    }

    /**
     * Search feature requests.
     */
    public function search(string $search, int $perPage = 15): LengthAwarePaginator
    {
        return $this->featureRequestRepository->search($search, $perPage);
    }

    /**
     * Get statistics.
     */
    public function getStatistics(): array
    {
        $cacheKey = 'feature_request_statistics';
        
        if (config('feature-requests.cache.enabled', true)) {
            return Cache::tags(['feature-requests'])->remember($cacheKey, config('feature-requests.cache.ttl', 3600), function () {
                return $this->featureRequestRepository->getStatistics();
            });
        }

        return $this->featureRequestRepository->getStatistics();
    }

    /**
     * Get public statistics (only for public feature requests).
     */
    public function getPublicStatistics(): array
    {
        $cacheKey = 'public_feature_request_statistics';
        
        if (config('feature-requests.cache.enabled', true)) {
            return Cache::tags(['feature-requests'])->remember($cacheKey, config('feature-requests.cache.ttl', 3600), function () {
                return $this->featureRequestRepository->getPublicStatistics();
            });
        }

        return $this->featureRequestRepository->getPublicStatistics();
    }

    /**
     * Get feature requests grouped by status for roadmap.
     */
    public function getForRoadmap(array $filters = []): array
    {
        $cacheKey = 'feature_requests_roadmap_' . md5(serialize($filters));
        
        if (config('feature-requests.cache.enabled', true)) {
            return Cache::tags(['feature-requests'])->remember($cacheKey, config('feature-requests.cache.ttl', 3600), function () use ($filters) {
                return $this->featureRequestRepository->getForRoadmap($filters);
            });
        }

        return $this->featureRequestRepository->getForRoadmap($filters);
    }

    /**
     * Get feature requests that need attention.
     */
    public function getNeedingAttention(): Collection
    {
        return $this->featureRequestRepository->getNeedingAttention();
    }

    /**
     * Update feature request status.
     */
    public function updateStatus(int $id, string $status): bool
    {
        $featureRequest = $this->find($id);
        
        if (!$featureRequest) {
            return false;
        }

        $oldStatus = $featureRequest->status;
        $result = $this->update($id, ['status' => $status]);

        if ($result && config('feature-requests.notifications.notify_on_status_change', true)) {
            // TODO: Send notification to user about status change
            // $this->notifyStatusChange($featureRequest, $oldStatus, $status);
        }

        return $result;
    }

    /**
     * Assign feature request to user.
     */
    public function assignTo(int $id, int $userId): bool
    {
        return $this->update($id, ['assigned_to' => $userId]);
    }

    /**
     * Feature/unfeature a feature request.
     */
    public function toggleFeatured(int $id): bool
    {
        $featureRequest = $this->find($id);
        
        if (!$featureRequest) {
            return false;
        }

        return $this->update($id, ['is_featured' => !$featureRequest->is_featured]);
    }

    /**
     * Increment view count.
     */
    public function incrementViewCount(int $id): void
    {
        $featureRequest = $this->find($id);
        
        if ($featureRequest) {
            $featureRequest->incrementViewCount();
        }
    }

    /**
     * Update vote count.
     */
    public function updateVoteCount(int $id): void
    {
        $featureRequest = $this->find($id);
        
        if ($featureRequest) {
            $featureRequest->updateVoteCount();
        }
    }

    /**
     * Update comment count.
     */
    public function updateCommentCount(int $id): void
    {
        $featureRequest = $this->find($id);
        
        if ($featureRequest) {
            $featureRequest->updateCommentCount();
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
     * Check if user can create feature request.
     */
    public function canCreate(): bool
    {
        return Auth::check() && Auth::user()->can(config('feature-requests.permissions.create_feature_request', 'create feature requests'));
    }

    /**
     * Check if user can edit feature request.
     */
    public function canEdit(FeatureRequest $featureRequest): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();

        // User can edit their own feature requests
        if ($featureRequest->user_id === $user->id) {
            return $user->can(config('feature-requests.permissions.edit_feature_request', 'edit feature requests'));
        }

        // Admin can edit any feature request
        return $user->can(config('feature-requests.permissions.manage_feature_requests', 'manage feature requests'));
    }

    /**
     * Check if user can delete feature request.
     */
    public function canDelete(FeatureRequest $featureRequest): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();

        // User can delete their own feature requests
        if ($featureRequest->user_id === $user->id) {
            return $user->can(config('feature-requests.permissions.delete_feature_request', 'delete feature requests'));
        }

        // Admin can delete any feature request
        return $user->can(config('feature-requests.permissions.manage_feature_requests', 'manage feature requests'));
    }
}
