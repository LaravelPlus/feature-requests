<?php

declare(strict_types=1);

namespace LaravelPlus\FeatureRequests\Contracts\DTOs;

interface ExtendedRequestDTOInterface extends DTOInterface
{
    /**
     * Create a DTO instance from an HTTP request with additional parameters.
     */
    public static function fromRequest($request, ...$parameters): static;

    /**
     * Validate the DTO data.
     */
    public function validate(): bool;

    /**
     * Get validation errors.
     */
    public function getValidationErrors(): array;
}
