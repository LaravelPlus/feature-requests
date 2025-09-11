<?php

declare(strict_types=1);

namespace LaravelPlus\FeatureRequests\DTOs;

use Illuminate\Http\Request;
use LaravelPlus\FeatureRequests\Contracts\DTOs\ExtendedRequestDTOInterface;

final class VoteDTO extends BaseExtendedRequestDTO implements ExtendedRequestDTOInterface
{
    public function __construct(
        public readonly int $featureRequestId,
        public readonly int $userId,
        public readonly string $voteType, // 'up' or 'down'
        public readonly ?string $ipAddress = null
    ) {}

    public static function fromRequest($request, ...$parameters): static
    {
        [$featureRequestId, $userId] = $parameters;
        
        return new self(
            featureRequestId: $featureRequestId,
            userId: $userId,
            voteType: $request->input('vote_type'),
            ipAddress: $request->ip()
        );
    }

    public static function fromArray(array $data): static
    {
        return new self(
            featureRequestId: $data['feature_request_id'],
            userId: $data['user_id'],
            voteType: $data['vote_type'],
            ipAddress: $data['ip_address'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'feature_request_id' => $this->featureRequestId,
            'user_id' => $this->userId,
            'vote_type' => $this->voteType,
            'ip_address' => $this->ipAddress,
        ];
    }

    public function isUpVote(): bool
    {
        return $this->voteType === 'up';
    }

    public function isDownVote(): bool
    {
        return $this->voteType === 'down';
    }

    /**
     * Get validation rules for the DTO.
     */
    protected function getValidationRules(): array
    {
        return [
            'feature_request_id' => ['required', 'integer', 'exists:feature_requests,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'vote_type' => ['required', 'string', 'in:up,down'],
            'ip_address' => ['nullable', 'ip'],
        ];
    }
}
