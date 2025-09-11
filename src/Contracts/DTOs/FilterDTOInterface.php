<?php

declare(strict_types=1);

namespace LaravelPlus\FeatureRequests\Contracts\DTOs;

interface FilterDTOInterface extends DTOInterface
{
    /**
     * Check if any filters are applied.
     */
    public function hasFilters(): bool;

    /**
     * Get the filters as query parameters.
     */
    public function toQueryParams(): array;

    /**
     * Merge with another filter DTO.
     */
    public function merge(FilterDTOInterface $other): static;

    /**
     * Reset all filters to default values.
     */
    public function reset(): static;
}
