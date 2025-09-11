<?php

declare(strict_types=1);

namespace LaravelPlus\FeatureRequests\Services;

use LaravelPlus\FeatureRequests\Repositories\CategoryRepository;
use LaravelPlus\FeatureRequests\Contracts\Services\CategoryServiceInterface;
use LaravelPlus\FeatureRequests\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class CategoryService implements CategoryServiceInterface
{
    protected CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Get all categories with pagination.
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->categoryRepository->paginate($perPage, $filters);
    }

    /**
     * Find a category by ID.
     */
    public function find(int $id): ?Category
    {
        return $this->categoryRepository->find($id);
    }


    /**
     * Get active categories.
     */
    public function getActive(): Collection
    {
        $cacheKey = 'feature_request_categories_active';
        
        if (config('feature-requests.cache.enabled', true)) {
            return Cache::tags(['feature-requests'])->remember($cacheKey, config('feature-requests.cache.ttl', 3600), function () {
                return $this->categoryRepository->getActive();
            });
        }

        return $this->categoryRepository->getActive();
    }

    /**
     * Find a category by slug.
     */
    public function findBySlug(string $slug): ?Category
    {
        return $this->categoryRepository->findBySlug($slug);
    }

    /**
     * Create a new category.
     */
    public function create(array $data): Category
    {
        $category = $this->categoryRepository->create($data);

        // Clear cache
        $this->clearCache();

        return $category;
    }

    /**
     * Update a category.
     */
    public function update(int $id, array $data): bool
    {
        $result = $this->categoryRepository->update($id, $data);

        if ($result) {
            // Clear cache
            $this->clearCache();
        }

        return $result;
    }

    /**
     * Delete a category.
     */
    public function delete(int $id): bool
    {
        $result = $this->categoryRepository->delete($id);

        if ($result) {
            // Clear cache
            $this->clearCache();
        }

        return $result;
    }

    /**
     * Get categories with feature request counts.
     */
    public function getWithCounts(): Collection
    {
        $cacheKey = 'feature_request_categories_with_counts';
        
        if (config('feature-requests.cache.enabled', true)) {
            return Cache::tags(['feature-requests'])->remember($cacheKey, config('feature-requests.cache.ttl', 3600), function () {
                return $this->categoryRepository->getWithCounts();
            });
        }

        return $this->categoryRepository->getWithCounts();
    }

    /**
     * Get active categories with feature request counts.
     */
    public function getActiveWithCounts(): Collection
    {
        $cacheKey = 'feature_request_categories_active_with_counts';
        
        if (config('feature-requests.cache.enabled', true)) {
            return Cache::tags(['feature-requests'])->remember($cacheKey, config('feature-requests.cache.ttl', 3600), function () {
                return $this->categoryRepository->getActiveWithCounts();
            });
        }

        return $this->categoryRepository->getActiveWithCounts();
    }

    /**
     * Get the default category.
     */
    public function getDefault(): ?Category
    {
        $cacheKey = 'feature_request_default_category';
        
        if (config('feature-requests.cache.enabled', true)) {
            return Cache::tags(['feature-requests'])->remember($cacheKey, config('feature-requests.cache.ttl', 3600), function () {
                return $this->categoryRepository->getDefault();
            });
        }

        return $this->categoryRepository->getDefault();
    }

    /**
     * Create default categories if they don't exist.
     */
    public function createDefaultCategories(): void
    {
        $this->categoryRepository->createDefaultCategories();
        
        // Clear cache
        $this->clearCache();
    }

    /**
     * Toggle category active status.
     */
    public function toggleActive(int $id): bool
    {
        $category = $this->find($id);
        
        if (!$category) {
            return false;
        }

        return $this->update($id, ['is_active' => !$category->is_active]);
    }

    /**
     * Update category sort order.
     */
    public function updateSortOrder(int $id, int $sortOrder): bool
    {
        return $this->update($id, ['sort_order' => $sortOrder]);
    }

    /**
     * Reorder categories.
     */
    public function reorder(array $categoryIds): bool
    {
        try {
            foreach ($categoryIds as $index => $categoryId) {
                $this->updateSortOrder($categoryId, $index + 1);
            }

            // Clear cache
            $this->clearCache();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get category statistics.
     */
    public function getStatistics(): array
    {
        $categories = $this->getWithCounts();
        
        $total = $categories->sum('feature_requests_count');
        $active = $categories->where('is_active', true)->sum('feature_requests_count');
        $inactive = $categories->where('is_active', false)->sum('feature_requests_count');

        return [
            'total_categories' => $categories->count(),
            'active_categories' => $categories->where('is_active', true)->count(),
            'inactive_categories' => $categories->where('is_active', false)->count(),
            'total_feature_requests' => $total,
            'active_feature_requests' => $active,
            'inactive_feature_requests' => $inactive,
        ];
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
     * Check if user can manage categories.
     */
    public function canManage(): bool
    {
        return auth()->check() && auth()->user()->can(config('feature-requests.permissions.manage_categories', 'manage feature request categories'));
    }

    /**
     * Check if user can create categories.
     */
    public function canCreate(): bool
    {
        return $this->canManage();
    }

    /**
     * Check if user can edit category.
     */
    public function canEdit(Category $category): bool
    {
        return $this->canManage();
    }

    /**
     * Check if user can delete category.
     */
    public function canDelete(Category $category): bool
    {
        return $this->canManage();
    }
}
