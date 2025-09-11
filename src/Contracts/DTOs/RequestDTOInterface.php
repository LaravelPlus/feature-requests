<?php

declare(strict_types=1);

namespace LaravelPlus\FeatureRequests\Contracts\DTOs;

use Illuminate\Http\Request;

interface RequestDTOInterface extends DTOInterface
{
    /**
     * Create a DTO instance from an HTTP request.
     */
    public static function fromRequest($request): static;

    /**
     * Validate the DTO data.
     */
    public function validate(): bool;

    /**
     * Get validation errors.
     */
    public function getValidationErrors(): array;
}
