<?php

declare(strict_types=1);

namespace LaravelPlus\FeatureRequests\DTOs;

use Illuminate\Http\Request;
use LaravelPlus\FeatureRequests\Contracts\DTOs\FilterDTOInterface;

final class FeatureRequestFiltersDTO extends BaseFilterDTO implements FilterDTOInterface
{
    public function __construct(
        public readonly ?string $status = null,
        public readonly ?int $categoryId = null,
        public readonly ?string $search = null,
        public readonly ?string $sortBy = 'created_at',
        public readonly ?string $sortDirection = 'desc',
        public readonly ?bool $isPublic = null,
        public readonly ?bool $isFeatured = null,
        public readonly ?int $userId = null,
        public readonly ?int $assignedTo = null
    ) {}

    public static function fromRequest($request): static
    {
        return new self(
            status: $request->input('status'),
            categoryId: $request->input('category_id'),
            search: $request->input('search'),
            sortBy: $request->input('sort_by', 'created_at'),
            sortDirection: $request->input('sort_direction', 'desc'),
            isPublic: $request->has('is_public') ? $request->boolean('is_public') : null,
            isFeatured: $request->has('is_featured') ? $request->boolean('is_featured') : null,
            userId: $request->input('user_id'),
            assignedTo: $request->input('assigned_to')
        );
    }

    public static function fromArray(array $data): static
    {
        return new self(
            status: $data['status'] ?? null,
            categoryId: $data['category_id'] ?? null,
            search: $data['search'] ?? null,
            sortBy: $data['sort_by'] ?? 'created_at',
            sortDirection: $data['sort_direction'] ?? 'desc',
            isPublic: $data['is_public'] ?? null,
            isFeatured: $data['is_featured'] ?? null,
            userId: $data['user_id'] ?? null,
            assignedTo: $data['assigned_to'] ?? null
        );
    }

    public function toArray(): array
    {
        $data = [];
        
        if ($this->status !== null) $data['status'] = $this->status;
        if ($this->categoryId !== null) $data['category_id'] = $this->categoryId;
        if ($this->search !== null) $data['search'] = $this->search;
        if ($this->sortBy !== 'created_at') $data['sort_by'] = $this->sortBy;
        if ($this->sortDirection !== 'desc') $data['sort_direction'] = $this->sortDirection;
        if ($this->isPublic !== null) $data['is_public'] = $this->isPublic;
        if ($this->isFeatured !== null) $data['is_featured'] = $this->isFeatured;
        if ($this->userId !== null) $data['user_id'] = $this->userId;
        if ($this->assignedTo !== null) $data['assigned_to'] = $this->assignedTo;
        
        return $data;
    }

    public function getCacheKey(): string
    {
        return 'feature_request_filters_' . md5(serialize($this->toArray()));
    }
}
