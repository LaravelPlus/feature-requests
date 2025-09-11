<?php

declare(strict_types=1);

namespace LaravelPlus\FeatureRequests\Contracts\Repositories;

use LaravelPlus\FeatureRequests\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface extends BaseRepositoryInterface
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
}