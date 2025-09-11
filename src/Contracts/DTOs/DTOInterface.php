<?php

declare(strict_types=1);

namespace LaravelPlus\FeatureRequests\Contracts\DTOs;

interface DTOInterface
{
    /**
     * Convert the DTO to an array.
     */
    public function toArray(): array;

    /**
     * Create a DTO instance from an array.
     */
    public static function fromArray(array $data): static;

    /**
     * Get the DTO as JSON string.
     */
    public function toJson(int $options = 0): string;

    /**
     * Check if the DTO has any data.
     */
    public function isEmpty(): bool;

    /**
     * Get a unique identifier for caching purposes.
     */
    public function getCacheKey(): string;
}
