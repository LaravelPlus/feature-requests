<?php

declare(strict_types=1);

namespace LaravelPlus\FeatureRequests\Contracts\Services;

use LaravelPlus\FeatureRequests\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryServiceInterface extends BaseServiceInterface
{
    /**
     * Get active categories.
     */
    public function getActive(): Collection;

    /**
     * Find a category by slug.
     */
    public function findBySlug(string $slug): ?Category;

    /**
     * Get categories with feature request counts.
     */
    public function getWithCounts(): Collection;

    /**
     * Get active categories with feature request counts.
     */
    public function getActiveWithCounts(): Collection;

    /**
     * Get default category.
     */
    public function getDefault(): ?Category;

    /**
     * Create default categories.
     */
    public function createDefaultCategories(): void;

    /**
     * Toggle category active status.
     */
    public function toggleActive(int $id): bool;

    /**
     * Update category sort order.
     */
    public function updateSortOrder(int $id, int $sortOrder): bool;

    /**
     * Reorder categories.
     */
    public function reorder(array $categoryIds): bool;

    /**
     * Get category statistics.
     */
    public function getStatistics(): array;

    /**
     * Check if user can manage categories.
     */
    public function canManage(): bool;

    /**
     * Check if user can create categories.
     */
    public function canCreate(): bool;

    /**
     * Check if user can edit a category.
     */
    public function canEdit(Category $category): bool;

    /**
     * Check if user can delete a category.
     */
    public function canDelete(Category $category): bool;
}