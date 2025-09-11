<?php

declare(strict_types=1);

namespace LaravelPlus\FeatureRequests\Http\Controllers;

use LaravelPlus\FeatureRequests\Services\CommentService;
use LaravelPlus\FeatureRequests\Services\FeatureRequestService;
use LaravelPlus\FeatureRequests\Http\Requests\StoreCommentRequest;
use LaravelPlus\FeatureRequests\Http\Requests\UpdateCommentRequest;
use LaravelPlus\FeatureRequests\Http\Resources\CommentResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\View\View;

final class CommentController extends Controller
{
    protected CommentService $commentService;
    protected FeatureRequestService $featureRequestService;

    public function __construct(
        CommentService $commentService,
        FeatureRequestService $featureRequestService
    ) {
        $this->commentService = $commentService;
        $this->featureRequestService = $featureRequestService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only(['feature_request_id', 'user_id', 'is_approved', 'is_pinned', 'parent_id']);
        $perPage = min($request->get('per_page', 15), 100);
        
        $comments = $this->commentService->paginate($perPage, $filters);

        return CommentResource::collection($comments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $slug)
    {
        // Get the feature request by slug
        $featureRequest = $this->featureRequestService->findBySlug($slug);
        
        if (!$featureRequest) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Feature request not found.'
                ], 404);
            }
            return redirect()->back()->with('error', 'Feature request not found.');
        }

        // Validate the request
        $validated = $request->validate([
            'content' => 'required|string|min:3|max:2000',
            'parent_id' => 'nullable|exists:feature_request_comments,id'
        ]);

        // Add feature_request_id to validated data
        $validated['feature_request_id'] = $featureRequest->id;

        if (!$this->commentService->canCommentOn($featureRequest->id)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'You cannot comment on this feature request.'
                ], 403);
            }
            return redirect()->back()->with('error', 'You cannot comment on this feature request.');
        }

        try {
            $comment = $this->commentService->create($validated);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Comment created successfully.',
                    'data' => new CommentResource($comment)
                ], 201);
            }

            return redirect()->back()->with('success', 'Comment posted successfully!');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage()
                ], 400);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): CommentResource
    {
        $comment = $this->commentService->find($id);
        
        if (!$comment) {
            abort(404, 'Comment not found.');
        }

        return new CommentResource($comment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommentRequest $request, int $id): JsonResponse
    {
        $comment = $this->commentService->find($id);
        
        if (!$comment) {
            abort(404, 'Comment not found.');
        }

        if (!$this->commentService->canEdit($comment)) {
            abort(403, 'You do not have permission to edit this comment.');
        }

        $this->commentService->update($id, $request->validated());

        return response()->json([
            'message' => 'Comment updated successfully.',
            'data' => new CommentResource($comment->fresh())
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $comment = $this->commentService->find($id);
        
        if (!$comment) {
            abort(404, 'Comment not found.');
        }

        if (!$this->commentService->canDelete($comment)) {
            abort(403, 'You do not have permission to delete this comment.');
        }

        $this->commentService->delete($id);

        return response()->json([
            'message' => 'Comment deleted successfully.'
        ]);
    }

    /**
     * Get comments for a feature request.
     */
    public function getByFeatureRequest(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'feature_request_id' => 'required|exists:feature_requests,id'
        ]);

        $comments = $this->commentService->getByFeatureRequest($request->feature_request_id);

        return CommentResource::collection($comments);
    }

    /**
     * Get top-level comments for a feature request.
     */
    public function getTopLevelByFeatureRequest(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'feature_request_id' => 'required|exists:feature_requests,id'
        ]);

        $comments = $this->commentService->getTopLevelByFeatureRequest($request->feature_request_id);

        return CommentResource::collection($comments);
    }

    /**
     * Get replies to a comment.
     */
    public function getReplies(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'parent_id' => 'required|exists:feature_request_comments,id'
        ]);

        $replies = $this->commentService->getReplies($request->parent_id);

        return CommentResource::collection($replies);
    }

    /**
     * Get comments by user.
     */
    public function getByUser(Request $request): AnonymousResourceCollection
    {
        $userId = $request->get('user_id', auth()->id());
        
        if (!$userId) {
            return response()->json([
                'message' => 'User ID is required.'
            ], 400);
        }

        $comments = $this->commentService->getByUser($userId);

        return CommentResource::collection($comments);
    }

    /**
     * Get approved comments.
     */
    public function getApproved(): AnonymousResourceCollection
    {
        $comments = $this->commentService->getApproved();

        return CommentResource::collection($comments);
    }

    /**
     * Get pending comments.
     */
    public function getPending(): AnonymousResourceCollection
    {
        if (!$this->commentService->canModerate()) {
            abort(403, 'You do not have permission to view pending comments.');
        }

        $comments = $this->commentService->getPending();

        return CommentResource::collection($comments);
    }

    /**
     * Get pinned comments.
     */
    public function getPinned(): AnonymousResourceCollection
    {
        $comments = $this->commentService->getPinned();

        return CommentResource::collection($comments);
    }

    /**
     * Approve a comment.
     */
    public function approve(int $id): JsonResponse
    {
        $comment = $this->commentService->find($id);
        
        if (!$comment) {
            abort(404, 'Comment not found.');
        }

        if (!$this->commentService->canModerate()) {
            abort(403, 'You do not have permission to moderate comments.');
        }

        $this->commentService->approve($id);

        return response()->json([
            'message' => 'Comment approved successfully.',
            'data' => new CommentResource($comment->fresh())
        ]);
    }

    /**
     * Pin a comment.
     */
    public function pin(int $id): JsonResponse
    {
        $comment = $this->commentService->find($id);
        
        if (!$comment) {
            abort(404, 'Comment not found.');
        }

        if (!$this->commentService->canModerate()) {
            abort(403, 'You do not have permission to pin comments.');
        }

        $this->commentService->pin($id);

        return response()->json([
            'message' => 'Comment pinned successfully.',
            'data' => new CommentResource($comment->fresh())
        ]);
    }

    /**
     * Unpin a comment.
     */
    public function unpin(int $id): JsonResponse
    {
        $comment = $this->commentService->find($id);
        
        if (!$comment) {
            abort(404, 'Comment not found.');
        }

        if (!$this->commentService->canModerate()) {
            abort(403, 'You do not have permission to unpin comments.');
        }

        $this->commentService->unpin($id);

        return response()->json([
            'message' => 'Comment unpinned successfully.',
            'data' => new CommentResource($comment->fresh())
        ]);
    }

    /**
     * Get recent comments.
     */
    public function getRecent(Request $request): AnonymousResourceCollection
    {
        $limit = min($request->get('limit', 10), 50);
        
        $comments = $this->commentService->getRecent($limit);

        return CommentResource::collection($comments);
    }

    /**
     * Search comments.
     */
    public function search(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'search' => 'required|string|min:3'
        ]);

        $perPage = min($request->get('per_page', 15), 100);
        
        $comments = $this->commentService->search($request->search, $perPage);

        return CommentResource::collection($comments);
    }

    /**
     * Get comment statistics.
     */
    public function statistics(): JsonResponse
    {
        $statistics = $this->commentService->getStatistics();
        
        return response()->json($statistics);
    }
}
