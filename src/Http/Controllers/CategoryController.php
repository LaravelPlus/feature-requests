<?php

declare(strict_types=1);

namespace LaravelPlus\FeatureRequests\Http\Controllers;

use LaravelPlus\FeatureRequests\Services\CategoryService;
use LaravelPlus\FeatureRequests\Http\Requests\StoreCategoryRequest;
use LaravelPlus\FeatureRequests\Http\Requests\UpdateCategoryRequest;
use LaravelPlus\FeatureRequests\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\View\View;

final class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the resource (Admin).
     */
    public function index(Request $request): View|AnonymousResourceCollection
    {
        $categories = $this->categoryService->getActiveWithCounts();

        if ($request->expectsJson()) {
            return CategoryResource::collection($categories);
        }

        return view('feature-requests::admin.categories.index', compact('categories'));
    }

    /**
     * Display a listing of public categories (Customer).
     */
    public function publicIndex(Request $request): View
    {
        $categories = $this->categoryService->getActiveWithCounts();

        return view('feature-requests::public.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('feature-requests::categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request): JsonResponse|View
    {
        if (!$this->categoryService->canCreate()) {
            abort(403, 'You do not have permission to create categories.');
        }

        $category = $this->categoryService->create($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Category created successfully.',
                'data' => new CategoryResource($category)
            ], 201);
        }

        return redirect()->route('feature-requests.categories.index')
                        ->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified resource (Admin).
     */
    public function show(string $slug): View|CategoryResource
    {
        $category = $this->categoryService->findBySlug($slug);
        
        if (!$category) {
            abort(404, 'Category not found.');
        }

        if (request()->expectsJson()) {
            return new CategoryResource($category);
        }

        return view('feature-requests::admin.categories.show', compact('category'));
    }

    /**
     * Display the specified public resource (Customer).
     */
    public function publicShow(string $slug): View
    {
        $category = $this->categoryService->findBySlug($slug);
        
        if (!$category) {
            abort(404, 'Category not found.');
        }

        return view('feature-requests::public.categories.show', compact('category'));
    }

    /**
     * Display admin listing of the resource.
     */
    public function adminIndex(Request $request): View
    {
        $categories = $this->categoryService->getActiveWithCounts();

        return view('feature-requests::admin.categories.index', compact('categories'));
    }

    /**
     * Display admin view of the specified resource.
     */
    public function adminShow(string $slug): View
    {
        $category = $this->categoryService->findBySlug($slug);
        
        if (!$category) {
            abort(404, 'Category not found.');
        }

        return view('feature-requests::admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $slug): View
    {
        $category = $this->categoryService->findBySlug($slug);
        
        if (!$category) {
            abort(404, 'Category not found.');
        }

        if (!$this->categoryService->canEdit($category)) {
            abort(403, 'You do not have permission to edit this category.');
        }

        return view('feature-requests::categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, string $slug): JsonResponse|View
    {
        $category = $this->categoryService->findBySlug($slug);
        
        if (!$category) {
            abort(404, 'Category not found.');
        }

        if (!$this->categoryService->canEdit($category)) {
            abort(403, 'You do not have permission to edit this category.');
        }

        $this->categoryService->update($category->id, $request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Category updated successfully.',
                'data' => new CategoryResource($category->fresh())
            ]);
        }

        return redirect()->route('feature-requests.categories.index')
                        ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug): JsonResponse|View
    {
        $category = $this->categoryService->findBySlug($slug);
        
        if (!$category) {
            abort(404, 'Category not found.');
        }

        if (!$this->categoryService->canDelete($category)) {
            abort(403, 'You do not have permission to delete this category.');
        }

        $this->categoryService->delete($category->id);

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Category deleted successfully.'
            ]);
        }

        return redirect()->route('feature-requests.categories.index')
                        ->with('success', 'Category deleted successfully.');
    }

    /**
     * Toggle active status of the category.
     */
    public function toggleActive(string $slug): JsonResponse
    {
        $category = $this->categoryService->findBySlug($slug);
        
        if (!$category) {
            abort(404, 'Category not found.');
        }

        if (!$this->categoryService->canEdit($category)) {
            abort(403, 'You do not have permission to edit this category.');
        }

        $this->categoryService->toggleActive($category->id);

        return response()->json([
            'message' => 'Category status updated successfully.',
            'data' => new CategoryResource($category->fresh())
        ]);
    }

    /**
     * Update sort order of categories.
     */
    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:feature_request_categories,id'
        ]);

        if (!$this->categoryService->canManage()) {
            abort(403, 'You do not have permission to reorder categories.');
        }

        $this->categoryService->reorder($request->category_ids);

        return response()->json([
            'message' => 'Categories reordered successfully.'
        ]);
    }

    /**
     * Get all categories (including inactive).
     */
    public function all(Request $request): AnonymousResourceCollection
    {
        $categories = $this->categoryService->all();

        return CategoryResource::collection($categories);
    }

    /**
     * Get active categories.
     */
    public function active(Request $request): AnonymousResourceCollection
    {
        $categories = $this->categoryService->getActive();

        return CategoryResource::collection($categories);
    }

    /**
     * Get categories with counts.
     */
    public function withCounts(Request $request): AnonymousResourceCollection
    {
        $categories = $this->categoryService->getWithCounts();

        return CategoryResource::collection($categories);
    }

    /**
     * Get active categories with counts.
     */
    public function activeWithCounts(Request $request): AnonymousResourceCollection
    {
        $categories = $this->categoryService->getActiveWithCounts();

        return CategoryResource::collection($categories);
    }

    /**
     * Get the default category.
     */
    public function default(Request $request): JsonResponse
    {
        $category = $this->categoryService->getDefault();

        if (!$category) {
            return response()->json([
                'message' => 'No default category found.'
            ], 404);
        }

        return response()->json([
            'data' => new CategoryResource($category)
        ]);
    }

    /**
     * Create default categories.
     */
    public function createDefaults(): JsonResponse
    {
        if (!$this->categoryService->canManage()) {
            abort(403, 'You do not have permission to create default categories.');
        }

        $this->categoryService->createDefaultCategories();

        return response()->json([
            'message' => 'Default categories created successfully.'
        ]);
    }

    /**
     * Get category statistics.
     */
    public function statistics(): JsonResponse
    {
        $statistics = $this->categoryService->getStatistics();
        
        return response()->json($statistics);
    }
}
