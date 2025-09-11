<?php

declare(strict_types=1);

namespace LaravelPlus\FeatureRequests\DTOs\Examples;

use LaravelPlus\FeatureRequests\DTOs\CreateFeatureRequestDTO;
use LaravelPlus\FeatureRequests\DTOs\UpdateFeatureRequestDTO;
use LaravelPlus\FeatureRequests\DTOs\FeatureRequestFiltersDTO;
use LaravelPlus\FeatureRequests\DTOs\VoteDTO;
use LaravelPlus\FeatureRequests\DTOs\CommentDTO;

/**
 * Example usage of the improved DTO system
 */
final class DTOUsageExample
{
    public function demonstrateCreateDTO(): void
    {
        // Create DTO from array
        $createDTO = CreateFeatureRequestDTO::fromArray([
            'title' => 'New Feature Request',
            'description' => 'This is a detailed description of the feature request',
            'additional_info' => 'Additional context and information',
            'status' => 'pending',
            'priority' => 'high',
            'category_id' => 1,
            'is_public' => true,
        ]);

        // Validate the DTO
        if ($createDTO->validate()) {
            echo "DTO is valid!\n";
        } else {
            echo "Validation errors: " . json_encode($createDTO->getValidationErrors()) . "\n";
        }

        // Convert to array
        $data = $createDTO->toArray();
        echo "DTO as array: " . json_encode($data) . "\n";

        // Get cache key
        echo "Cache key: " . $createDTO->getCacheKey() . "\n";

        // Check if empty
        echo "Is empty: " . ($createDTO->isEmpty() ? 'Yes' : 'No') . "\n";
    }

    public function demonstrateUpdateDTO(): void
    {
        // Create update DTO with only changed fields
        $updateDTO = UpdateFeatureRequestDTO::fromArray([
            'title' => 'Updated Title',
            'status' => 'in_progress',
            'priority' => 'critical',
        ]);

        // Check if has changes
        echo "Has changes: " . ($updateDTO->hasChanges() ? 'Yes' : 'No') . "\n";

        // Get only changed fields
        $changes = $updateDTO->toArray();
        echo "Changes: " . json_encode($changes) . "\n";
    }

    public function demonstrateFilterDTO(): void
    {
        // Create filter DTO
        $filters = FeatureRequestFiltersDTO::fromArray([
            'status' => 'pending',
            'category_id' => 1,
            'search' => 'important feature',
            'sort_by' => 'created_at',
            'sort_direction' => 'desc',
        ]);

        // Check if has filters
        echo "Has filters: " . ($filters->hasFilters() ? 'Yes' : 'No') . "\n";

        // Get active filters
        $activeFilters = $filters->getActiveFilters();
        echo "Active filters: " . json_encode($activeFilters) . "\n";

        // Get query parameters
        $queryParams = $filters->toQueryParams();
        echo "Query params: " . json_encode($queryParams) . "\n";

        // Get cache key
        echo "Cache key: " . $filters->getCacheKey() . "\n";
    }

    public function demonstrateVoteDTO(): void
    {
        // Create vote DTO
        $voteDTO = VoteDTO::fromArray([
            'feature_request_id' => 1,
            'user_id' => 1,
            'vote_type' => 'up',
            'ip_address' => '192.168.1.1',
        ]);

        // Check vote type
        echo "Is up vote: " . ($voteDTO->isUpVote() ? 'Yes' : 'No') . "\n";
        echo "Is down vote: " . ($voteDTO->isDownVote() ? 'Yes' : 'No') . "\n";

        // Validate
        if ($voteDTO->validate()) {
            echo "Vote DTO is valid!\n";
        } else {
            echo "Vote validation errors: " . json_encode($voteDTO->getValidationErrors()) . "\n";
        }
    }

    public function demonstrateCommentDTO(): void
    {
        // Create comment DTO
        $commentDTO = CommentDTO::fromArray([
            'feature_request_id' => 1,
            'user_id' => 1,
            'content' => 'This is a great idea!',
            'parent_id' => null,
        ]);

        // Check if it's a reply
        echo "Is reply: " . ($commentDTO->isReply() ? 'Yes' : 'No') . "\n";

        // Validate
        if ($commentDTO->validate()) {
            echo "Comment DTO is valid!\n";
        } else {
            echo "Comment validation errors: " . json_encode($commentDTO->getValidationErrors()) . "\n";
        }
    }

    public function demonstrateBaseDTOFeatures(): void
    {
        $createDTO = CreateFeatureRequestDTO::fromArray([
            'title' => 'Test Feature',
            'description' => 'Test Description',
        ]);

        // Get property value
        echo "Title: " . $createDTO->get('title') . "\n";

        // Check if property exists
        echo "Has title: " . ($createDTO->has('title') ? 'Yes' : 'No') . "\n";
        echo "Has invalid_prop: " . ($createDTO->has('invalid_prop') ? 'Yes' : 'No') . "\n";

        // Get all property names
        $properties = $createDTO->getPropertyNames();
        echo "Properties: " . implode(', ', $properties) . "\n";

        // Create new instance with updates
        $updatedDTO = $createDTO->with(['status' => 'completed', 'priority' => 'high']);
        echo "Updated status: " . $updatedDTO->get('status') . "\n";
    }
}
