<?php

namespace LaravelPlus\FeatureRequests\Repositories;

use LaravelPlus\FeatureRequests\Models\Comment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CommentRepository
{
    protected Comment $model;

    public function __construct(Comment $model)
    {
        $this->model = $model;
    }

    /**
     * Get all comments.
     */
    public function all(): Collection
    {
        return $this->model->with(['user', 'featureRequest', 'parent'])->get();
    }

    /**
     * Get comments with pagination.
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        // Apply filters
        if (isset($filters['feature_request_id'])) {
            $query->where('feature_request_id', $filters['feature_request_id']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['is_approved'])) {
            $query->where('is_approved', $filters['is_approved']);
        }

        if (isset($filters['is_pinned'])) {
            $query->where('is_pinned', $filters['is_pinned']);
        }

        if (isset($filters['parent_id'])) {
            $query->where('parent_id', $filters['parent_id']);
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'asc';

        switch ($sortBy) {
            case 'pinned':
                $query->orderBy('is_pinned', 'desc')->orderBy('created_at', $sortDirection);
                break;
            default:
                $query->orderBy($sortBy, $sortDirection);
        }

        return $query->with(['user', 'featureRequest', 'parent', 'replies.user'])
                    ->paginate($perPage);
    }

    /**
     * Find a comment by ID.
     */
    public function find(int $id): ?Comment
    {
        return $this->model->with(['user', 'featureRequest', 'parent', 'replies.user'])->find($id);
    }

    /**
     * Create a new comment.
     */
    public function create(array $data): Comment
    {
        return $this->model->create($data);
    }

    /**
     * Update a comment.
     */
    public function update(int $id, array $data): bool
    {
        $comment = $this->find($id);
        
        if (!$comment) {
            return false;
        }

        return $comment->update($data);
    }

    /**
     * Delete a comment.
     */
    public function delete(int $id): bool
    {
        $comment = $this->find($id);
        
        if (!$comment) {
            return false;
        }

        return $comment->delete();
    }

    /**
     * Get comments for a feature request.
     */
    public function getByFeatureRequest(int $featureRequestId): Collection
    {
        return $this->model->where('feature_request_id', $featureRequestId)
                          ->approved()
                          ->ordered()
                          ->with(['user', 'replies.user'])
                          ->get();
    }

    /**
     * Get top-level comments for a feature request.
     */
    public function getTopLevelByFeatureRequest(int $featureRequestId): Collection
    {
        return $this->model->where('feature_request_id', $featureRequestId)
                          ->topLevel()
                          ->approved()
                          ->ordered()
                          ->with(['user', 'replies.user'])
                          ->get();
    }

    /**
     * Get replies to a comment.
     */
    public function getReplies(int $parentId): Collection
    {
        return $this->model->where('parent_id', $parentId)
                          ->approved()
                          ->ordered()
                          ->with(['user'])
                          ->get();
    }

    /**
     * Get comments by user.
     */
    public function getByUser(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)
                          ->with(['featureRequest', 'parent'])
                          ->get();
    }

    /**
     * Get approved comments.
     */
    public function getApproved(): Collection
    {
        return $this->model->approved()
                          ->with(['user', 'featureRequest', 'parent'])
                          ->get();
    }

    /**
     * Get pending comments.
     */
    public function getPending(): Collection
    {
        return $this->model->where('is_approved', false)
                          ->with(['user', 'featureRequest', 'parent'])
                          ->get();
    }

    /**
     * Get pinned comments.
     */
    public function getPinned(): Collection
    {
        return $this->model->pinned()
                          ->with(['user', 'featureRequest', 'parent'])
                          ->get();
    }

    /**
     * Get comment count for a feature request.
     */
    public function getCountByFeatureRequest(int $featureRequestId): int
    {
        return $this->model->where('feature_request_id', $featureRequestId)
                          ->approved()
                          ->count();
    }

    /**
     * Get comment count by user.
     */
    public function getCountByUser(int $userId): int
    {
        return $this->model->where('user_id', $userId)->count();
    }

    /**
     * Approve a comment.
     */
    public function approve(int $id): bool
    {
        return $this->update($id, ['is_approved' => true]);
    }

    /**
     * Pin a comment.
     */
    public function pin(int $id): bool
    {
        return $this->update($id, ['is_pinned' => true]);
    }

    /**
     * Unpin a comment.
     */
    public function unpin(int $id): bool
    {
        return $this->update($id, ['is_pinned' => false]);
    }

    /**
     * Get recent comments.
     */
    public function getRecent(int $limit = 10): Collection
    {
        return $this->model->approved()
                          ->orderBy('created_at', 'desc')
                          ->with(['user', 'featureRequest'])
                          ->limit($limit)
                          ->get();
    }

    /**
     * Search comments.
     */
    public function search(string $search, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->where('content', 'like', "%{$search}%")
                          ->approved()
                          ->with(['user', 'featureRequest', 'parent'])
                          ->paginate($perPage);
    }
}
