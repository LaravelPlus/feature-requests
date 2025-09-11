<?php

declare(strict_types=1);

namespace LaravelPlus\FeatureRequests\DTOs;

use LaravelPlus\FeatureRequests\Contracts\DTOs\RequestDTOInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

abstract class BaseRequestDTO extends BaseDTO implements RequestDTOInterface
{
    protected array $validationErrors = [];

    /**
     * Validate the DTO data.
     */
    public function validate(): bool
    {
        $validator = Validator::make($this->toArray(), $this->getValidationRules());
        
        if ($validator->fails()) {
            $this->validationErrors = $validator->errors()->toArray();
            return false;
        }
        
        $this->validationErrors = [];
        return true;
    }

    /**
     * Get validation errors.
     */
    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }

    /**
     * Get validation rules for the DTO.
     * Override this method in child classes.
     */
    abstract protected function getValidationRules(): array;

    /**
     * Check if the DTO has validation errors.
     */
    public function hasValidationErrors(): bool
    {
        return !empty($this->validationErrors);
    }

    /**
     * Get the first validation error message.
     */
    public function getFirstError(): ?string
    {
        if (empty($this->validationErrors)) {
            return null;
        }
        
        $firstError = reset($this->validationErrors);
        return is_array($firstError) ? reset($firstError) : $firstError;
    }

    /**
     * Get all validation error messages as a flat array.
     */
    public function getAllErrorMessages(): array
    {
        $messages = [];
        
        foreach ($this->validationErrors as $field => $errors) {
            if (is_array($errors)) {
                $messages = array_merge($messages, $errors);
            } else {
                $messages[] = $errors;
            }
        }
        
        return $messages;
    }
}
