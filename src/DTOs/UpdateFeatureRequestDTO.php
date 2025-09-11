<?php

declare(strict_types=1);

namespace LaravelPlus\FeatureRequests\DTOs;

use LaravelPlus\FeatureRequests\Http\Requests\UpdateFeatureRequestRequest;
use LaravelPlus\FeatureRequests\Contracts\DTOs\RequestDTOInterface;

final class UpdateFeatureRequestDTO extends BaseRequestDTO implements RequestDTOInterface
{
    public function __construct(
        public readonly ?string $title = null,
        public readonly ?string $description = null,
        public readonly ?string $additionalInfo = null,
        public readonly ?string $status = null,
        public readonly ?string $priority = null,
        public readonly ?int $categoryId = null,
        public readonly ?int $assignedTo = null,
        public readonly ?string $dueDate = null,
        public readonly ?int $estimatedEffort = null,
        public readonly ?array $tags = null,
        public readonly ?bool $isPublic = null,
        public readonly ?bool $isFeatured = null
    ) {}

    public static function fromRequest($request): static
    {
        return new self(
            title: $request->input('title'),
            description: $request->input('description'),
            additionalInfo: $request->input('additional_info'),
            status: $request->input('status'),
            priority: $request->input('priority'),
            categoryId: $request->input('category_id'),
            assignedTo: $request->input('assigned_to'),
            dueDate: $request->input('due_date'),
            estimatedEffort: $request->input('estimated_effort'),
            tags: $request->input('tags'),
            isPublic: $request->has('is_public') ? $request->boolean('is_public') : null,
            isFeatured: $request->has('is_featured') ? $request->boolean('is_featured') : null
        );
    }

    public static function fromArray(array $data): static
    {
        return new self(
            title: $data['title'] ?? null,
            description: $data['description'] ?? null,
            additionalInfo: $data['additional_info'] ?? null,
            status: $data['status'] ?? null,
            priority: $data['priority'] ?? null,
            categoryId: $data['category_id'] ?? null,
            assignedTo: $data['assigned_to'] ?? null,
            dueDate: $data['due_date'] ?? null,
            estimatedEffort: $data['estimated_effort'] ?? null,
            tags: $data['tags'] ?? null,
            isPublic: $data['is_public'] ?? null,
            isFeatured: $data['is_featured'] ?? null
        );
    }

    public function toArray(): array
    {
        $data = [];
        
        if ($this->title !== null) $data['title'] = $this->title;
        if ($this->description !== null) $data['description'] = $this->description;
        if ($this->additionalInfo !== null) $data['additional_info'] = $this->additionalInfo;
        if ($this->status !== null) $data['status'] = $this->status;
        if ($this->priority !== null) $data['priority'] = $this->priority;
        if ($this->categoryId !== null) $data['category_id'] = $this->categoryId;
        if ($this->assignedTo !== null) $data['assigned_to'] = $this->assignedTo;
        if ($this->dueDate !== null) $data['due_date'] = $this->dueDate;
        if ($this->estimatedEffort !== null) $data['estimated_effort'] = $this->estimatedEffort;
        if ($this->tags !== null) $data['tags'] = $this->tags;
        if ($this->isPublic !== null) $data['is_public'] = $this->isPublic;
        if ($this->isFeatured !== null) $data['is_featured'] = $this->isFeatured;
        
        return $data;
    }

    public function hasChanges(): bool
    {
        return !empty($this->toArray());
    }

    /**
     * Get validation rules for the DTO.
     */
    protected function getValidationRules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'min:5', 'max:255'],
            'description' => ['sometimes', 'string', 'min:10'],
            'additional_info' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'status' => ['sometimes', 'string', 'in:pending,under_review,planned,in_progress,completed,rejected'],
            'priority' => ['sometimes', 'string', 'in:low,medium,high,critical'],
            'category_id' => ['sometimes', 'nullable', 'integer', 'exists:feature_request_categories,id'],
            'assigned_to' => ['sometimes', 'nullable', 'integer', 'exists:users,id'],
            'due_date' => ['sometimes', 'nullable', 'date', 'after:today'],
            'estimated_effort' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:1000'],
            'tags' => ['sometimes', 'array'],
            'tags.*' => ['string', 'max:50'],
            'is_public' => ['sometimes', 'boolean'],
            'is_featured' => ['sometimes', 'boolean'],
        ];
    }
}
