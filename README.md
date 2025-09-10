# LaravelPlus Feature Requests

A comprehensive Laravel feature requests package with voting system, categories, and status management.

## Features

- **Feature Request Management**: Create, edit, and manage feature requests
- **Voting System**: Users can vote up/down on feature requests
- **Categories**: Organize feature requests into categories
- **Comments**: Users can comment on feature requests with threaded replies
- **Status Management**: Track feature request status (pending, under review, planned, in progress, completed, rejected)
- **Priority Levels**: Set priority levels (low, medium, high, critical)
- **User Assignment**: Assign feature requests to specific users
- **Search & Filtering**: Search and filter feature requests
- **Statistics**: Get comprehensive statistics about feature requests
- **Permissions**: Role-based permissions for different actions
- **Caching**: Built-in caching for better performance
- **API & Web Routes**: Both API and web interfaces
- **Vue.js Components**: Ready-to-use Vue.js components

## Installation

1. Add the package to your `composer.json`:

```json
{
    "require": {
        "laravelplus/feature-requests": "@dev"
    }
}
```

2. Add the repository to your `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "./packages/laravelplus/feature-requests"
        }
    ]
}
```

3. Register the service provider in `bootstrap/providers.php`:

```php
LaravelPlus\FeatureRequests\Providers\FeatureRequestsServiceProvider::class,
```

4. Run composer install:

```bash
composer install
```

5. Publish and run migrations:

```bash
php artisan vendor:publish --tag=feature-requests-migrations
php artisan migrate
```

6. Publish configuration (optional):

```bash
php artisan vendor:publish --tag=feature-requests-config
```

7. Create default categories:

```bash
php artisan tinker
>>> LaravelPlus\FeatureRequests\Facades\FeatureRequests::categories()->createDefaultCategories();
```

## Configuration

The package comes with a comprehensive configuration file. You can publish it using:

```bash
php artisan vendor:publish --tag=feature-requests-config
```

Key configuration options:

- **Statuses**: Define available statuses and their colors
- **Voting**: Configure voting behavior and limits
- **Comments**: Configure commenting behavior
- **Categories**: Configure category settings
- **Permissions**: Define required permissions
- **Notifications**: Configure notification settings
- **Cache**: Configure caching settings

## Usage

### Basic Usage

```php
use LaravelPlus\FeatureRequests\Facades\FeatureRequests;

// Create a feature request
$featureRequest = FeatureRequests::featureRequests()->create([
    'title' => 'Add dark mode support',
    'description' => 'Users have requested dark mode support for better accessibility.',
    'category_id' => 1,
    'priority' => 'medium',
    'tags' => ['ui', 'accessibility']
]);

// Vote on a feature request
FeatureRequests::votes()->vote($featureRequest->id, 'up');

// Add a comment
FeatureRequests::comments()->create([
    'feature_request_id' => $featureRequest->id,
    'content' => 'This would be a great addition!'
]);
```

### API Usage

The package provides comprehensive API endpoints:

#### Feature Requests
- `GET /api/feature-requests` - List feature requests
- `POST /api/feature-requests` - Create feature request
- `GET /api/feature-requests/{slug}` - Get feature request
- `PUT /api/feature-requests/{slug}` - Update feature request
- `DELETE /api/feature-requests/{slug}` - Delete feature request

#### Voting
- `POST /api/feature-requests/votes` - Vote on feature request
- `DELETE /api/feature-requests/votes` - Remove vote
- `GET /api/feature-requests/votes/statistics` - Get vote statistics

#### Comments
- `GET /api/feature-requests/comments` - List comments
- `POST /api/feature-requests/comments` - Create comment
- `PUT /api/feature-requests/comments/{id}` - Update comment
- `DELETE /api/feature-requests/comments/{id}` - Delete comment

#### Categories
- `GET /api/feature-requests/categories` - List categories
- `POST /api/feature-requests/categories` - Create category
- `PUT /api/feature-requests/categories/{slug}` - Update category
- `DELETE /api/feature-requests/categories/{slug}` - Delete category

### Vue.js Components

The package includes ready-to-use Vue.js components:

```vue
<template>
  <div>
    <!-- Feature Request Card -->
    <FeatureRequestCard :feature-request="featureRequest" />
    
    <!-- Vote Button -->
    <VoteButton 
      :feature-request-id="featureRequest.id"
      :initial-vote-count="featureRequest.vote_count"
      @vote-changed="handleVoteChanged"
    />
  </div>
</template>

<script setup>
import FeatureRequestCard from '@/Components/FeatureRequestCard.vue'
import VoteButton from '@/Components/VoteButton.vue'
</script>
```

### Permissions

The package uses Laravel's permission system. Make sure to create the following permissions:

- `create feature requests`
- `edit feature requests`
- `delete feature requests`
- `vote on feature requests`
- `comment on feature requests`
- `manage feature requests`
- `manage feature request categories`

## Models

### FeatureRequest

The main model for feature requests with relationships to users, categories, votes, and comments.

### Vote

Represents user votes on feature requests with support for up/down voting.

### Category

Organizes feature requests into categories with customizable colors and icons.

### Comment

Allows users to comment on feature requests with support for threaded replies.

## Services

The package follows the repository pattern with dedicated services:

- `FeatureRequestService`: Main service for feature request operations
- `VoteService`: Handles voting operations
- `CategoryService`: Manages categories
- `CommentService`: Handles comments

## Repositories

Data access layer with repositories for each model:

- `FeatureRequestRepository`
- `VoteRepository`
- `CategoryRepository`
- `CommentRepository`

## Testing

Run the test suite:

```bash
composer test
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For support, please open an issue on the GitHub repository.
