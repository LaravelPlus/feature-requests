<?php

declare(strict_types=1);

namespace LaravelPlus\FeatureRequests\DTOs;

use LaravelPlus\FeatureRequests\Contracts\DTOs\FilterDTOInterface;

abstract class BaseFilterDTO extends BaseDTO implements FilterDTOInterface
{
    /**
     * Check if any filters are applied.
     */
    public function hasFilters(): bool
    {
        $data = $this->toArray();
        return !empty(array_filter($data, fn($value) => $value !== null && $value !== ''));
    }

    /**
     * Get the filters as query parameters.
     */
    public function toQueryParams(): array
    {
        return array_filter($this->toArray(), fn($value) => $value !== null && $value !== '');
    }

    /**
     * Merge with another filter DTO.
     */
    public function merge(FilterDTOInterface $other): static
    {
        $thisData = $this->toArray();
        $otherData = $other->toArray();
        
        // Merge arrays, with other DTO values taking precedence
        $merged = array_merge($thisData, $otherData);
        
        return static::fromArray($merged);
    }

    /**
     * Reset all filters to default values.
     */
    public function reset(): static
    {
        $defaults = $this->getDefaultValues();
        return static::fromArray($defaults);
    }

    /**
     * Get default values for the filter.
     * Override this method in child classes.
     */
    protected function getDefaultValues(): array
    {
        $reflection = new \ReflectionClass($this);
        $defaults = [];
        
        foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->isReadonly()) {
                $defaults[$property->getName()] = null;
            }
        }
        
        return $defaults;
    }

    /**
     * Get active filters (non-null, non-empty values).
     */
    public function getActiveFilters(): array
    {
        return array_filter($this->toArray(), function ($value) {
            return $value !== null && $value !== '' && $value !== [];
        });
    }

    /**
     * Check if a specific filter is active.
     */
    public function hasFilter(string $filterName): bool
    {
        $value = $this->get($filterName);
        return $value !== null && $value !== '' && $value !== [];
    }

    /**
     * Get the count of active filters.
     */
    public function getActiveFilterCount(): int
    {
        return count($this->getActiveFilters());
    }
}
