<?php

declare(strict_types=1);

namespace LaravelPlus\FeatureRequests\DTOs;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use LaravelPlus\FeatureRequests\Contracts\DTOs\DTOInterface;

final class FeatureRequestListDTO extends BaseDTO implements DTOInterface
{
    public function __construct(
        public readonly Collection|LengthAwarePaginator $featureRequests,
        public readonly array $pagination = [],
        public readonly array $statistics = [],
        public readonly array $filters = []
    ) {}

    public static function fromPaginator(LengthAwarePaginator $paginator, array $statistics = [], array $filters = []): static
    {
        return new self(
            featureRequests: $paginator,
            pagination: [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
            statistics: $statistics,
            filters: $filters
        );
    }

    public static function fromCollection(Collection $collection, array $statistics = [], array $filters = []): static
    {
        return new self(
            featureRequests: $collection,
            pagination: [],
            statistics: $statistics,
            filters: $filters
        );
    }

    public function getCacheKey(): string
    {
        $key = 'feature_request_list_' . md5(serialize($this->filters));
        if (!empty($this->pagination)) {
            $key .= '_page_' . $this->pagination['current_page'];
        }
        return $key;
    }

    public function hasPagination(): bool
    {
        return !empty($this->pagination);
    }

    public function getCount(): int
    {
        if ($this->hasPagination()) {
            return $this->pagination['total'];
        }
        
        return $this->featureRequests->count();
    }
}
