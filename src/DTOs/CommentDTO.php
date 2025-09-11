<?php

declare(strict_types=1);

namespace LaravelPlus\FeatureRequests\DTOs;

use Illuminate\Http\Request;
use LaravelPlus\FeatureRequests\Contracts\DTOs\ExtendedRequestDTOInterface;

final class CommentDTO extends BaseExtendedRequestDTO implements ExtendedRequestDTOInterface
{
    public function __construct(
        public readonly int $featureRequestId,
        public readonly int $userId,
        public readonly string $content,
        public readonly ?int $parentId = null,
        public readonly ?string $ipAddress = null
    ) {}

    public static function fromRequest($request, ...$parameters): static
    {
        [$featureRequestId, $userId] = $parameters;
        
        return new self(
            featureRequestId: $featureRequestId,
            userId: $userId,
            content: $request->input('content'),
            parentId: $request->input('parent_id'),
            ipAddress: $request->ip()
        );
    }

    public static function fromArray(array $data): static
    {
        return new self(
            featureRequestId: $data['feature_request_id'],
            userId: $data['user_id'],
            content: $data['content'],
            parentId: $data['parent_id'] ?? null,
            ipAddress: $data['ip_address'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'feature_request_id' => $this->featureRequestId,
            'user_id' => $this->userId,
            'content' => $this->content,
            'parent_id' => $this->parentId,
            'ip_address' => $this->ipAddress,
        ];
    }

    public function isReply(): bool
    {
        return $this->parentId !== null;
    }

    /**
     * Get validation rules for the DTO.
     */
    protected function getValidationRules(): array
    {
        return [
            'feature_request_id' => ['required', 'integer', 'exists:feature_requests,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'content' => ['required', 'string', 'min:1', 'max:2000'],
            'parent_id' => ['nullable', 'integer', 'exists:comments,id'],
            'ip_address' => ['nullable', 'ip'],
        ];
    }
}
