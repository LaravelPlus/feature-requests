<?php

declare(strict_types=1);

namespace LaravelPlus\FeatureRequests\Repositories;

use LaravelPlus\FeatureRequests\Models\Category;
use Illuminate\Database\Eloquent\Collection;

final class CategoryRepository
{
    protected Category $model;

    public function __construct(Category $model)
    {
        $this->model = $model;
    }

    /**
     * Get all categories.
     */
    public function all(): Collection
    {
        return $this->model->ordered()->get();
    }

    /**
     * Get active categories.
     */
    public function getActive(): Collection
    {
        return $this->model->active()->ordered()->get();
    }

    /**
     * Find a category by ID.
     */
    public function find(int $id): ?Category
    {
        return $this->model->find($id);
    }

    /**
     * Find a category by slug.
     */
    public function findBySlug(string $slug): ?Category
    {
        return $this->model->where('slug', $slug)->first();
    }

    /**
     * Create a new category.
     */
    public function create(array $data): Category
    {
        return $this->model->create($data);
    }

    /**
     * Update a category.
     */
    public function update(int $id, array $data): bool
    {
        $category = $this->find($id);
        
        if (!$category) {
            return false;
        }

        return $category->update($data);
    }

    /**
     * Delete a category.
     */
    public function delete(int $id): bool
    {
        $category = $this->find($id);
        
        if (!$category) {
            return false;
        }

        return $category->delete();
    }

    /**
     * Get categories with feature request counts.
     */
    public function getWithCounts(): Collection
    {
        return $this->model->withCount('featureRequests')
                          ->ordered()
                          ->get();
    }

    /**
     * Get active categories with feature request counts.
     */
    public function getActiveWithCounts(): Collection
    {
        return $this->model->active()
                          ->withCount('featureRequests')
                          ->ordered()
                          ->get();
    }

    /**
     * Get the default category.
     */
    public function getDefault(): ?Category
    {
        $defaultSlug = config('feature-requests.categories.default_category', 'general');
        
        return $this->findBySlug($defaultSlug) ?? $this->model->first();
    }

    /**
     * Create default categories if they don't exist.
     */
    public function createDefaultCategories(): void
    {
        $defaultCategories = [
            [
                'name' => 'General',
                'slug' => 'general',
                'description' => 'General feature requests',
                'color' => '#3B82F6',
                'icon' => 'fas fa-lightbulb',
                'sort_order' => 1,
            ],
            [
                'name' => 'User Interface',
                'slug' => 'user-interface',
                'description' => 'User interface and experience improvements',
                'color' => '#10B981',
                'icon' => 'fas fa-palette',
                'sort_order' => 2,
            ],
            [
                'name' => 'Performance',
                'slug' => 'performance',
                'description' => 'Performance and optimization requests',
                'color' => '#F59E0B',
                'icon' => 'fas fa-tachometer-alt',
                'sort_order' => 3,
            ],
            [
                'name' => 'Integration',
                'slug' => 'integration',
                'description' => 'Third-party integrations and APIs',
                'color' => '#8B5CF6',
                'icon' => 'fas fa-plug',
                'sort_order' => 4,
            ],
            [
                'name' => 'Security',
                'slug' => 'security',
                'description' => 'Security and privacy enhancements',
                'color' => '#EF4444',
                'icon' => 'fas fa-shield-alt',
                'sort_order' => 5,
            ],
        ];

        foreach ($defaultCategories as $categoryData) {
            if (!$this->findBySlug($categoryData['slug'])) {
                $this->create($categoryData);
            }
        }
    }
}
