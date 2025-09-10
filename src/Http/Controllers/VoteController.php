<?php

namespace LaravelPlus\FeatureRequests\Http\Controllers;

use LaravelPlus\FeatureRequests\Services\VoteService;
use LaravelPlus\FeatureRequests\Services\FeatureRequestService;
use LaravelPlus\FeatureRequests\Http\Requests\VoteRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VoteController extends Controller
{
    protected VoteService $voteService;
    protected FeatureRequestService $featureRequestService;

    public function __construct(
        VoteService $voteService,
        FeatureRequestService $featureRequestService
    ) {
        $this->voteService = $voteService;
        $this->featureRequestService = $featureRequestService;
    }

    /**
     * Store a vote on a feature request (alias for vote method).
     */
    public function store(Request $request, string $slug): JsonResponse
    {
        // Get the feature request by slug
        $featureRequest = $this->featureRequestService->findBySlug($slug);
        
        if (!$featureRequest) {
            return response()->json([
                'message' => 'Feature request not found.'
            ], 404);
        }

        if (!$this->voteService->canVoteOn($featureRequest->id)) {
            return response()->json([
                'message' => 'You cannot vote on this feature request.'
            ], 403);
        }

        try {
            $vote = $this->voteService->vote(
                $featureRequest->id,
                'up', // Default to up vote for simple voting
                null
            );

            $statistics = $this->voteService->getVoteStatistics($featureRequest->id);

            return response()->json([
                'message' => 'Vote recorded successfully.',
                'data' => [
                    'vote' => $vote,
                    'statistics' => $statistics
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Remove a vote from a feature request (alias for removeVote method).
     */
    public function destroy(Request $request, string $slug): JsonResponse
    {
        // Get the feature request by slug
        $featureRequest = $this->featureRequestService->findBySlug($slug);
        
        if (!$featureRequest) {
            return response()->json([
                'message' => 'Feature request not found.'
            ], 404);
        }

        try {
            $this->voteService->removeVote($featureRequest->id);

            $statistics = $this->voteService->getVoteStatistics($featureRequest->id);

            return response()->json([
                'message' => 'Vote removed successfully.',
                'data' => [
                    'statistics' => $statistics
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Vote on a feature request.
     */
    public function vote(VoteRequest $request): JsonResponse
    {
        if (!$this->voteService->canVoteOn($request->feature_request_id)) {
            return response()->json([
                'message' => 'You cannot vote on this feature request.'
            ], 403);
        }

        try {
            $vote = $this->voteService->vote(
                $request->feature_request_id,
                $request->vote_type,
                $request->comment
            );

            $statistics = $this->voteService->getVoteStatistics($request->feature_request_id);

            return response()->json([
                'message' => 'Vote recorded successfully.',
                'data' => [
                    'vote' => $vote,
                    'statistics' => $statistics
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Remove vote from a feature request.
     */
    public function removeVote(Request $request): JsonResponse
    {
        $request->validate([
            'feature_request_id' => 'required|exists:feature_requests,id'
        ]);

        try {
            $this->voteService->removeVote($request->feature_request_id);

            $statistics = $this->voteService->getVoteStatistics($request->feature_request_id);

            return response()->json([
                'message' => 'Vote removed successfully.',
                'data' => [
                    'statistics' => $statistics
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get votes for a feature request.
     */
    public function getVotes(Request $request): JsonResponse
    {
        $request->validate([
            'feature_request_id' => 'required|exists:feature_requests,id'
        ]);

        $votes = $this->voteService->getVotes($request->feature_request_id);
        $statistics = $this->voteService->getVoteStatistics($request->feature_request_id);

        return response()->json([
            'data' => [
                'votes' => $votes,
                'statistics' => $statistics
            ]
        ]);
    }

    /**
     * Get up votes for a feature request.
     */
    public function getUpVotes(Request $request): JsonResponse
    {
        $request->validate([
            'feature_request_id' => 'required|exists:feature_requests,id'
        ]);

        $upVotes = $this->voteService->getUpVotes($request->feature_request_id);

        return response()->json([
            'data' => $upVotes
        ]);
    }

    /**
     * Get down votes for a feature request.
     */
    public function getDownVotes(Request $request): JsonResponse
    {
        $request->validate([
            'feature_request_id' => 'required|exists:feature_requests,id'
        ]);

        $downVotes = $this->voteService->getDownVotes($request->feature_request_id);

        return response()->json([
            'data' => $downVotes
        ]);
    }

    /**
     * Get votes by user.
     */
    public function getUserVotes(Request $request): JsonResponse
    {
        $userId = $request->get('user_id', auth()->id());
        
        if (!$userId) {
            return response()->json([
                'message' => 'User ID is required.'
            ], 400);
        }

        $votes = $this->voteService->getVotesByUser($userId);
        $statistics = $this->voteService->getUserVotingStatistics($userId);

        return response()->json([
            'data' => [
                'votes' => $votes,
                'statistics' => $statistics
            ]
        ]);
    }

    /**
     * Get up votes by user.
     */
    public function getUserUpVotes(Request $request): JsonResponse
    {
        $userId = $request->get('user_id', auth()->id());
        
        if (!$userId) {
            return response()->json([
                'message' => 'User ID is required.'
            ], 400);
        }

        $upVotes = $this->voteService->getUpVotesByUser($userId);

        return response()->json([
            'data' => $upVotes
        ]);
    }

    /**
     * Get down votes by user.
     */
    public function getUserDownVotes(Request $request): JsonResponse
    {
        $userId = $request->get('user_id', auth()->id());
        
        if (!$userId) {
            return response()->json([
                'message' => 'User ID is required.'
            ], 400);
        }

        $downVotes = $this->voteService->getDownVotesByUser($userId);

        return response()->json([
            'data' => $downVotes
        ]);
    }

    /**
     * Get vote statistics for a feature request.
     */
    public function getStatistics(Request $request): JsonResponse
    {
        $request->validate([
            'feature_request_id' => 'required|exists:feature_requests,id'
        ]);

        $statistics = $this->voteService->getVoteStatistics($request->feature_request_id);

        return response()->json([
            'data' => $statistics
        ]);
    }

    /**
     * Check if user has voted on a feature request.
     */
    public function hasVoted(Request $request): JsonResponse
    {
        $request->validate([
            'feature_request_id' => 'required|exists:feature_requests,id'
        ]);

        $hasVoted = $this->voteService->hasUserVoted($request->feature_request_id);
        $voteType = $this->voteService->getUserVoteType($request->feature_request_id);

        return response()->json([
            'data' => [
                'has_voted' => $hasVoted,
                'vote_type' => $voteType
            ]
        ]);
    }

    /**
     * Get most voted feature requests.
     */
    public function getMostVoted(Request $request): JsonResponse
    {
        $limit = min($request->get('limit', 10), 50);
        
        $featureRequests = $this->voteService->getMostVotedFeatureRequests($limit);

        return response()->json([
            'data' => $featureRequests
        ]);
    }

    /**
     * Get user's voting statistics.
     */
    public function getUserStatistics(Request $request): JsonResponse
    {
        $userId = $request->get('user_id', auth()->id());
        
        if (!$userId) {
            return response()->json([
                'message' => 'User ID is required.'
            ], 400);
        }

        $statistics = $this->voteService->getUserVotingStatistics($userId);

        return response()->json([
            'data' => $statistics
        ]);
    }
}
