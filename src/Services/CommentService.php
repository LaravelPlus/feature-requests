<?php

declare(strict_types=1);

namespace LaravelPlus\FeatureRequests\Services;

use LaravelPlus\FeatureRequests\Repositories\CommentRepository;
use LaravelPlus\FeatureRequests\Contracts\Services\CommentServiceInterface;
use LaravelPlus\FeatureRequests\Repositories\FeatureRequestRepository;
use LaravelPlus\FeatureRequests\Models\Comment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

final class CommentService implements CommentServiceInterface
{
    protected CommentRepository $commentRepository;
    protected FeatureRequestRepository $featureRequestRepository;

    public function __construct(
        CommentRepository $commentRepository,
        FeatureRequestRepository $featureRequestRepository
    ) {
        $this->commentRepository = $commentRepository;
        $this->featureRequestRepository = $featureRequestRepository;
    }

    /**
     * Get all comments with pagination.
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->commentRepository->paginate($perPage, $filters);
    }


    /**
     * Find a comment by ID.
     */
    public function find(int $id): ?Comment
    {
        return $this->commentRepository->find($id);
    }

    /**
     * Create a new comment.
     */
    public function create(array $data): Comment
    {
        // Set default values
        $data['user_id'] = $data['user_id'] ?? Auth::id();
        $data['is_approved'] = $data['is_approved'] ?? !config('feature-requests.comments.moderation_required', false);
        $data['is_pinned'] = $data['is_pinned'] ?? false;

        $comment = $this->commentRepository->create($data);

        // Update comment count on feature request
        $this->updateCommentCount($data['feature_request_id']);

        return $comment;
    }

    /**
     * Update a comment.
     */
    public function update(int $id, array $data): bool
    {
        return $this->commentRepository->update($id, $data);
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

        $featureRequestId = $comment->feature_request_id;
        $result = $this->commentRepository->delete($id);

        if ($result) {
            // Update comment count on feature request
            $this->updateCommentCount($featureRequestId);
        }

        return $result;
    }

    /**
     * Get comments for a feature request.
     */
    public function getByFeatureRequest(int $featureRequestId): Collection
    {
        return $this->commentRepository->getByFeatureRequest($featureRequestId);
    }

    /**
     * Get top-level comments for a feature request.
     */
    public function getTopLevelByFeatureRequest(int $featureRequestId): Collection
    {
        return $this->commentRepository->getTopLevelByFeatureRequest($featureRequestId);
    }

    /**
     * Get replies to a comment.
     */
    public function getReplies(int $parentId): Collection
    {
        return $this->commentRepository->getReplies($parentId);
    }

    /**
     * Get comments by user.
     */
    public function getByUser(int $userId): Collection
    {
        return $this->commentRepository->getByUser($userId);
    }

    /**
     * Get approved comments.
     */
    public function getApproved(): Collection
    {
        return $this->commentRepository->getApproved();
    }

    /**
     * Get pending comments.
     */
    public function getPending(): Collection
    {
        return $this->commentRepository->getPending();
    }

    /**
     * Get pinned comments.
     */
    public function getPinned(): Collection
    {
        return $this->commentRepository->getPinned();
    }

    /**
     * Get comment count for a feature request.
     */
    public function getCountByFeatureRequest(int $featureRequestId): int
    {
        return $this->commentRepository->getCountByFeatureRequest($featureRequestId);
    }

    /**
     * Get comment count by user.
     */
    public function getCountByUser(int $userId): int
    {
        return $this->commentRepository->getCountByUser($userId);
    }

    /**
     * Approve a comment.
     */
    public function approve(int $id): bool
    {
        $comment = $this->find($id);
        
        if (!$comment) {
            return false;
        }

        $result = $this->commentRepository->approve($id);

        if ($result) {
            // Update comment count on feature request
            $this->updateCommentCount($comment->feature_request_id);
        }

        return $result;
    }

    /**
     * Pin a comment.
     */
    public function pin(int $id): bool
    {
        return $this->commentRepository->pin($id);
    }

    /**
     * Unpin a comment.
     */
    public function unpin(int $id): bool
    {
        return $this->commentRepository->unpin($id);
    }

    /**
     * Get recent comments.
     */
    public function getRecent(int $limit = 10): Collection
    {
        return $this->commentRepository->getRecent($limit);
    }

    /**
     * Search comments.
     */
    public function search(string $search, int $perPage = 15): LengthAwarePaginator
    {
        return $this->commentRepository->search($search, $perPage);
    }

    /**
     * Update comment count for a feature request.
     */
    protected function updateCommentCount(int $featureRequestId): void
    {
        $featureRequest = $this->featureRequestRepository->find($featureRequestId);
        
        if ($featureRequest) {
            $featureRequest->updateCommentCount();
        }
    }

    /**
     * Check if user can comment.
     */
    public function canComment(): bool
    {
        if (!config('feature-requests.comments.enabled', true)) {
            return false;
        }

        if (config('feature-requests.comments.require_authentication', true) && !Auth::check()) {
            return false;
        }

        if (config('feature-requests.comments.allow_anonymous_comments', false) && !Auth::check()) {
            return true;
        }

        return Auth::user()->can(config('feature-requests.permissions.comment_feature_request', 'comment on feature requests'));
    }

    /**
     * Check if user can comment on a specific feature request.
     */
    public function canCommentOn(int $featureRequestId): bool
    {
        if (!$this->canComment()) {
            return false;
        }

        $featureRequest = $this->featureRequestRepository->find($featureRequestId);
        
        if (!$featureRequest) {
            return false;
        }

        return $featureRequest->canBeCommentedOn();
    }

    /**
     * Check if user can edit comment.
     */
    public function canEdit(Comment $comment): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();

        // User can edit their own comments
        if ($comment->user_id === $user->id) {
            return true;
        }

        // Admin can edit any comment
        return $user->can(config('feature-requests.permissions.manage_feature_requests', 'manage feature requests'));
    }

    /**
     * Check if user can delete comment.
     */
    public function canDelete(Comment $comment): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();

        // User can delete their own comments
        if ($comment->user_id === $user->id) {
            return true;
        }

        // Admin can delete any comment
        return $user->can(config('feature-requests.permissions.manage_feature_requests', 'manage feature requests'));
    }

    /**
     * Check if user can moderate comments.
     */
    public function canModerate(): bool
    {
        return Auth::check() && Auth::user()->can(config('feature-requests.permissions.manage_feature_requests', 'manage feature requests'));
    }

    /**
     * Get comment statistics.
     */
    public function getStatistics(): array
    {
        return [
            'total_comments' => $this->commentRepository->all()->count(),
            'approved_comments' => $this->commentRepository->getApproved()->count(),
            'pending_comments' => $this->commentRepository->getPending()->count(),
            'pinned_comments' => $this->commentRepository->getPinned()->count(),
        ];
    }
}
