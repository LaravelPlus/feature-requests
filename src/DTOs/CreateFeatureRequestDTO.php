<?php

declare(strict_types=1);

namespace LaravelPlus\FeatureRequests\DTOs;

use Illuminate\Http\Request;
use LaravelPlus\FeatureRequests\Http\Requests\StoreFeatureRequestRequest;
use LaravelPlus\FeatureRequests\Contracts\DTOs\RequestDTOInterface;

final class CreateFeatureRequestDTO extends BaseRequestDTO implements RequestDTOInterface
{
    public function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly ?string $additionalInfo = null,
        public readonly string $status = 'pending',
        public readonly string $priority = 'medium',
        public readonly ?int $categoryId = null,
        public readonly ?int $userId = null,
        public readonly ?int $assignedTo = null,
        public readonly ?string $dueDate = null,
        public readonly ?int $estimatedEffort = null,
        public readonly array $tags = [],
        public readonly bool $isPublic = true,
        public readonly bool $isFeatured = false
    ) {}

    public static function fromRequest($request): static
    {
        return new self(
            title: $request->input('title'),
            description: $request->input('description'),
            additionalInfo: $request->input('additional_info'),
            status: $request->input('status', 'pending'),
            priority: $request->input('priority', 'medium'),
            categoryId: $request->input('category_id'),
            userId: $request->input('user_id'),
            assignedTo: $request->input('assigned_to'),
            dueDate: $request->input('due_date'),
            estimatedEffort: $request->input('estimated_effort'),
            tags: $request->input('tags', []),
            isPublic: $request->boolean('is_public', true),
            isFeatured: $request->boolean('is_featured', false)
        );
    }

    public static function fromArray(array $data): static
    {
        return new self(
            title: $data['title'],
            description: $data['description'],
            additionalInfo: $data['additional_info'] ?? null,
            status: $data['status'] ?? 'pending',
            priority: $data['priority'] ?? 'medium',
            categoryId: $data['category_id'] ?? null,
            userId: $data['user_id'] ?? null,
            assignedTo: $data['assigned_to'] ?? null,
            dueDate: $data['due_date'] ?? null,
            estimatedEffort: $data['estimated_effort'] ?? null,
            tags: $data['tags'] ?? [],
            isPublic: $data['is_public'] ?? true,
            isFeatured: $data['is_featured'] ?? false
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'additional_info' => $this->additionalInfo,
            'status' => $this->status,
            'priority' => $this->priority,
            'category_id' => $this->categoryId,
            'user_id' => $this->userId,
            'assigned_to' => $this->assignedTo,
            'due_date' => $this->dueDate,
            'estimated_effort' => $this->estimatedEffort,
            'tags' => $this->tags,
            'is_public' => $this->isPublic,
            'is_featured' => $this->isFeatured,
        ];
    }

    public function withDefaults(array $defaults = []): static
    {
        return new self(
            title: $this->title,
            description: $this->description,
            additionalInfo: $this->additionalInfo,
            status: $this->status,
            priority: $this->priority,
            categoryId: $this->categoryId,
            userId: $this->userId ?? $defaults['user_id'] ?? null,
            assignedTo: $this->assignedTo,
            dueDate: $this->dueDate,
            estimatedEffort: $this->estimatedEffort,
            tags: $this->tags,
            isPublic: $this->isPublic,
            isFeatured: $this->isFeatured
        );
    }

    /**
     * Get validation rules for the DTO.
     */
    protected function getValidationRules(): array
    {
        return [
            'title' => ['required', 'string', 'min:5', 'max:255'],
            'description' => ['required', 'string', 'min:10'],
            'additional_info' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'string', 'in:pending,under_review,planned,in_progress,completed,rejected'],
            'priority' => ['required', 'string', 'in:low,medium,high,critical'],
            'category_id' => ['nullable', 'integer', 'exists:feature_request_categories,id'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'due_date' => ['nullable', 'date', 'after:today'],
            'estimated_effort' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'tags' => ['array'],
            'tags.*' => ['string', 'max:50'],
            'is_public' => ['boolean'],
            'is_featured' => ['boolean'],
        ];
    }
}
