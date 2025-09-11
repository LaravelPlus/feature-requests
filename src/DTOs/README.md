# Feature Requests DTO System

This package implements a comprehensive Data Transfer Object (DTO) system for the Feature Requests functionality, providing type safety, validation, and a clean contract-based architecture.

## Architecture Overview

The DTO system is built on a contract-based architecture with the following components:

### Contracts (Interfaces)
- `DTOInterface` - Base contract for all DTOs
- `RequestDTOInterface` - For DTOs that can be created from HTTP requests
- `ExtendedRequestDTOInterface` - For DTOs that require additional parameters
- `FilterDTOInterface` - For DTOs that handle filtering functionality

### Base Classes
- `BaseDTO` - Common functionality for all DTOs
- `BaseRequestDTO` - Base class for request-based DTOs with validation
- `BaseExtendedRequestDTO` - Base class for DTOs with additional parameters
- `BaseFilterDTO` - Base class for filter DTOs

### DTOs
- `CreateFeatureRequestDTO` - For creating new feature requests
- `UpdateFeatureRequestDTO` - For updating existing feature requests
- `FeatureRequestFiltersDTO` - For filtering feature requests
- `VoteDTO` - For handling votes on feature requests
- `CommentDTO` - For handling comments on feature requests
- `FeatureRequestListDTO` - For paginated feature request lists

## Key Features

### 1. Type Safety
All DTOs use PHP 8.1+ readonly properties with strict typing, ensuring data integrity.

### 2. Validation
Built-in validation using Laravel's Validator with comprehensive rules for each DTO type.

### 3. Multiple Creation Methods
- `fromArray()` - Create from array data
- `fromRequest()` - Create from HTTP request (with additional parameters for some DTOs)

### 4. Caching Support
Each DTO generates a unique cache key for efficient caching strategies.

### 5. Serialization
- `toArray()` - Convert to array
- `toJson()` - Convert to JSON string

### 6. Utility Methods
- `isEmpty()` - Check if DTO has data
- `has()` - Check if property exists
- `get()` - Get property value
- `with()` - Create new instance with updates

## Usage Examples

### Creating a Feature Request

```php
use LaravelPlus\FeatureRequests\DTOs\CreateFeatureRequestDTO;

// From array
$createDTO = CreateFeatureRequestDTO::fromArray([
    'title' => 'New Feature Request',
    'description' => 'Detailed description of the feature',
    'status' => 'pending',
    'priority' => 'high',
    'is_public' => true,
]);

// Validate
if ($createDTO->validate()) {
    // Process the DTO
    $data = $createDTO->toArray();
} else {
    $errors = $createDTO->getValidationErrors();
}
```

### Updating a Feature Request

```php
use LaravelPlus\FeatureRequests\DTOs\UpdateFeatureRequestDTO;

$updateDTO = UpdateFeatureRequestDTO::fromArray([
    'title' => 'Updated Title',
    'status' => 'completed',
]);

// Check if there are changes
if ($updateDTO->hasChanges()) {
    $changes = $updateDTO->toArray();
    // Apply changes
}
```

### Filtering Feature Requests

```php
use LaravelPlus\FeatureRequests\DTOs\FeatureRequestFiltersDTO;

$filters = FeatureRequestFiltersDTO::fromArray([
    'status' => 'pending',
    'search' => 'important',
    'category_id' => 1,
]);

// Check if filters are applied
if ($filters->hasFilters()) {
    $activeFilters = $filters->getActiveFilters();
    $queryParams = $filters->toQueryParams();
}
```

### Handling Votes

```php
use LaravelPlus\FeatureRequests\DTOs\VoteDTO;

$voteDTO = VoteDTO::fromArray([
    'feature_request_id' => 1,
    'user_id' => 1,
    'vote_type' => 'up',
]);

// Check vote type
if ($voteDTO->isUpVote()) {
    // Handle up vote
}
```

### Handling Comments

```php
use LaravelPlus\FeatureRequests\DTOs\CommentDTO;

$commentDTO = CommentDTO::fromArray([
    'feature_request_id' => 1,
    'user_id' => 1,
    'content' => 'This is a comment',
    'parent_id' => null, // null for top-level comment
]);

// Check if it's a reply
if ($commentDTO->isReply()) {
    // Handle reply
}
```

## Validation Rules

### CreateFeatureRequestDTO
- `title`: required, string, min:5, max:255
- `description`: required, string, min:10
- `additional_info`: nullable, string, max:2000
- `status`: required, in:pending,under_review,planned,in_progress,completed,rejected
- `priority`: required, in:low,medium,high,critical
- `category_id`: nullable, integer, exists:feature_request_categories,id
- `user_id`: nullable, integer, exists:users,id
- `assigned_to`: nullable, integer, exists:users,id
- `due_date`: nullable, date, after:today
- `estimated_effort`: nullable, integer, min:1, max:1000
- `tags`: array
- `tags.*`: string, max:50
- `is_public`: boolean
- `is_featured`: boolean

### UpdateFeatureRequestDTO
Same as CreateFeatureRequestDTO but with `sometimes` rule for all fields.

### VoteDTO
- `feature_request_id`: required, integer, exists:feature_requests,id
- `user_id`: required, integer, exists:users,id
- `vote_type`: required, in:up,down
- `ip_address`: nullable, ip

### CommentDTO
- `feature_request_id`: required, integer, exists:feature_requests,id
- `user_id`: required, integer, exists:users,id
- `content`: required, string, min:1, max:2000
- `parent_id`: nullable, integer, exists:comments,id
- `ip_address`: nullable, ip

## Benefits

1. **Type Safety**: Compile-time type checking prevents runtime errors
2. **Clear Contracts**: Interfaces define expected behavior
3. **Validation**: Built-in validation with comprehensive rules
4. **Caching**: Automatic cache key generation
5. **Serialization**: Easy conversion to arrays and JSON
6. **Extensibility**: Easy to add new DTOs following the same patterns
7. **Testability**: DTOs can be easily tested in isolation
8. **IDE Support**: Full autocomplete and type hints

## Best Practices

1. Always validate DTOs before processing
2. Use specific DTOs for different operations (Create, Update, etc.)
3. Leverage the `with()` method for creating modified versions
4. Use cache keys for efficient caching strategies
5. Handle validation errors gracefully
6. Use the appropriate base class for new DTOs

## Extending the System

To create a new DTO:

1. Choose the appropriate base class
2. Implement the required interface
3. Define readonly properties
4. Add validation rules
5. Add any specific methods needed

Example:

```php
use LaravelPlus\FeatureRequests\DTOs\BaseRequestDTO;
use LaravelPlus\FeatureRequests\Contracts\DTOs\RequestDTOInterface;

class MyCustomDTO extends BaseRequestDTO implements RequestDTOInterface
{
    public function __construct(
        public readonly string $name,
        public readonly int $value,
    ) {}

    protected function getValidationRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'value' => ['required', 'integer', 'min:1'],
        ];
    }
}
```
