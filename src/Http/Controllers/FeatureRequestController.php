<?php

namespace LaravelPlus\FeatureRequests\Http\Controllers;

use LaravelPlus\FeatureRequests\Services\FeatureRequestService;
use LaravelPlus\FeatureRequests\Services\CategoryService;
use LaravelPlus\FeatureRequests\Http\Requests\StoreFeatureRequestRequest;
use LaravelPlus\FeatureRequests\Http\Requests\UpdateFeatureRequestRequest;
use LaravelPlus\FeatureRequests\Http\Resources\FeatureRequestResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\View\View;

class FeatureRequestController extends Controller
{
    protected FeatureRequestService $featureRequestService;
    protected CategoryService $categoryService;

    public function __construct(
        FeatureRequestService $featureRequestService,
        CategoryService $categoryService
    ) {
        $this->featureRequestService = $featureRequestService;
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|AnonymousResourceCollection
    {
        $filters = $request->only(['status', 'category_id', 'search', 'sort_by', 'sort_direction']);
        $perPage = min($request->get('per_page', 15), config('feature-requests.pagination.max_per_page', 100));
        
        $featureRequests = $this->featureRequestService->paginate($perPage, $filters);
        $categories = $this->categoryService->getActiveWithCounts();
        $statistics = $this->featureRequestService->getStatistics();

        if ($request->expectsJson()) {
            return FeatureRequestResource::collection($featureRequests);
        }

        return view('feature-requests::index', compact('featureRequests', 'categories', 'statistics'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categories = $this->categoryService->getActive();
        
        return view('feature-requests::create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFeatureRequestRequest $request): JsonResponse|View
    {
        if (!$this->featureRequestService->canCreate()) {
            abort(403, 'You do not have permission to create feature requests.');
        }

        $featureRequest = $this->featureRequestService->create($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Feature request created successfully.',
                'data' => new FeatureRequestResource($featureRequest)
            ], 201);
        }

        return redirect()->route('feature-requests.show', $featureRequest->slug)
                        ->with('success', 'Feature request created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug): View|FeatureRequestResource
    {
        $featureRequest = $this->featureRequestService->findBySlug($slug);
        
        if (!$featureRequest) {
            abort(404, 'Feature request not found.');
        }

        // Increment view count
        $this->featureRequestService->incrementViewCount($featureRequest->id);

        if (request()->expectsJson()) {
            return new FeatureRequestResource($featureRequest);
        }

        return view('feature-requests::show', compact('featureRequest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $slug): View
    {
        $featureRequest = $this->featureRequestService->findBySlug($slug);
        
        if (!$featureRequest) {
            abort(404, 'Feature request not found.');
        }

        if (!$this->featureRequestService->canEdit($featureRequest)) {
            abort(403, 'You do not have permission to edit this feature request.');
        }

        $categories = $this->categoryService->getActive();
        
        return view('feature-requests::edit', compact('featureRequest', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFeatureRequestRequest $request, string $slug): JsonResponse|View
    {
        $featureRequest = $this->featureRequestService->findBySlug($slug);
        
        if (!$featureRequest) {
            abort(404, 'Feature request not found.');
        }

        if (!$this->featureRequestService->canEdit($featureRequest)) {
            abort(403, 'You do not have permission to edit this feature request.');
        }

        $this->featureRequestService->update($featureRequest->id, $request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Feature request updated successfully.',
                'data' => new FeatureRequestResource($featureRequest->fresh())
            ]);
        }

        return redirect()->route('feature-requests.show', $featureRequest->slug)
                        ->with('success', 'Feature request updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug): JsonResponse|View
    {
        $featureRequest = $this->featureRequestService->findBySlug($slug);
        
        if (!$featureRequest) {
            abort(404, 'Feature request not found.');
        }

        if (!$this->featureRequestService->canDelete($featureRequest)) {
            abort(403, 'You do not have permission to delete this feature request.');
        }

        $this->featureRequestService->delete($featureRequest->id);

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Feature request deleted successfully.'
            ]);
        }

        return redirect()->route('feature-requests.index')
                        ->with('success', 'Feature request deleted successfully.');
    }

    /**
     * Update the status of the specified resource.
     */
    public function updateStatus(Request $request, string $slug): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,under_review,planned,in_progress,completed,rejected'
        ]);

        $featureRequest = $this->featureRequestService->findBySlug($slug);
        
        if (!$featureRequest) {
            abort(404, 'Feature request not found.');
        }

        if (!$this->featureRequestService->canEdit($featureRequest)) {
            abort(403, 'You do not have permission to update this feature request.');
        }

        $this->featureRequestService->updateStatus($featureRequest->id, $request->status);

        return response()->json([
            'message' => 'Feature request status updated successfully.',
            'data' => new FeatureRequestResource($featureRequest->fresh())
        ]);
    }

    /**
     * Assign the feature request to a user.
     */
    public function assign(Request $request, string $slug): JsonResponse
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,id'
        ]);

        $featureRequest = $this->featureRequestService->findBySlug($slug);
        
        if (!$featureRequest) {
            abort(404, 'Feature request not found.');
        }

        if (!$this->featureRequestService->canEdit($featureRequest)) {
            abort(403, 'You do not have permission to assign this feature request.');
        }

        $this->featureRequestService->assignTo($featureRequest->id, $request->assigned_to);

        return response()->json([
            'message' => 'Feature request assigned successfully.',
            'data' => new FeatureRequestResource($featureRequest->fresh())
        ]);
    }

    /**
     * Toggle featured status of the feature request.
     */
    public function toggleFeatured(string $slug): JsonResponse
    {
        $featureRequest = $this->featureRequestService->findBySlug($slug);
        
        if (!$featureRequest) {
            abort(404, 'Feature request not found.');
        }

        if (!$this->featureRequestService->canEdit($featureRequest)) {
            abort(403, 'You do not have permission to feature this request.');
        }

        $this->featureRequestService->toggleFeatured($featureRequest->id);

        return response()->json([
            'message' => 'Feature request featured status updated successfully.',
            'data' => new FeatureRequestResource($featureRequest->fresh())
        ]);
    }

    /**
     * Get feature requests that need attention.
     */
    public function needingAttention(): AnonymousResourceCollection
    {
        $featureRequests = $this->featureRequestService->getNeedingAttention();
        
        return FeatureRequestResource::collection($featureRequests);
    }

    /**
     * Get statistics.
     */
    public function statistics(): JsonResponse
    {
        $statistics = $this->featureRequestService->getStatistics();
        
        return response()->json($statistics);
    }
}
