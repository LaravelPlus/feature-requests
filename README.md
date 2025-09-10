# LaravelPlus Feature Requests

A comprehensive Laravel package for managing feature requests with voting, commenting, and categorization capabilities. Built with modern design principles and a beautiful shadcn/ui inspired interface.

## ✨ Features

- **Feature Request Management**: Create, edit, and manage feature requests
- **Voting System**: Users can vote on feature requests
- **Comments & Discussions**: Threaded comments for each feature request
- **Categorization**: Organize requests with categories
- **Status Tracking**: Track request status (pending, in progress, completed, rejected)
- **Priority Levels**: Set priority levels (low, medium, high)
- **User Management**: User authentication and authorization
- **Modern UI**: Beautiful shadcn/ui inspired admin interface
- **API Support**: Full REST API for all operations
- **Search & Filtering**: Advanced search and filtering capabilities
- **Responsive Design**: Mobile-friendly interface
- **Soft Deletes**: Safe deletion with recovery options

## 🚀 Installation

### Step 1: Install via Composer

```bash
composer require laravelplus/feature-requests
```

### Step 2: Publish Configuration

```bash
php artisan vendor:publish --provider="LaravelPlus\FeatureRequests\Providers\FeatureRequestsServiceProvider"
```

### Step 3: Run Migrations

```bash
php artisan migrate
```

### Step 4: Create Default Categories (Optional)

```bash
php artisan feature-requests:create-default-categories
```

## 📋 Configuration

The package configuration is located in `config/feature-requests.php`:

```php
return [
    'middleware' => ['web', 'auth'],
    'prefix' => 'feature-requests',
    'user' => [
        'model' => App\Models\User::class,
    ],
    'default_categories' => [
        'User Interface',
        'Performance',
        'Security',
        'API',
        'Mobile',
        'Integration',
    ],
];
```

## 🎯 Usage

### Basic Usage

#### Creating a Feature Request

```php
use LaravelPlus\FeatureRequests\Models\FeatureRequest;

$featureRequest = FeatureRequest::create([
    'title' => 'Dark Mode Support',
    'description' => 'Add dark mode theme to the application',
    'category_id' => 1,
    'priority' => 'medium',
    'user_id' => auth()->id(),
    'is_public' => true,
]);
```

#### Voting on Feature Requests

```php
use LaravelPlus\FeatureRequests\Models\Vote;

$vote = Vote::create([
    'user_id' => auth()->id(),
    'feature_request_id' => $featureRequest->id,
    'vote_type' => 'up',
]);
```

#### Adding Comments

```php
use LaravelPlus\FeatureRequests\Models\Comment;

$comment = Comment::create([
    'user_id' => auth()->id(),
    'feature_request_id' => $featureRequest->id,
    'content' => 'This would be a great addition!',
]);
```

### Web Routes

The package provides the following web routes:

```php
// Feature Requests
GET    /feature-requests              // Index
GET    /feature-requests/create       // Create form
POST   /feature-requests              // Store
GET    /feature-requests/{slug}       // Show
GET    /feature-requests/{slug}/edit  // Edit form
PUT    /feature-requests/{slug}       // Update
DELETE /feature-requests/{slug}       // Destroy

// Voting
POST   /feature-requests/{slug}/vote  // Vote
DELETE /feature-requests/{slug}/vote  // Unvote

// Comments
POST   /feature-requests/{slug}/comments  // Store comment
PUT    /feature-requests/comments/{comment}  // Update comment
DELETE /feature-requests/comments/{comment}  // Delete comment

// Categories
GET    /feature-requests/categories              // Index
GET    /feature-requests/categories/create       // Create form
POST   /feature-requests/categories              // Store
GET    /feature-requests/categories/{slug}       // Show
GET    /feature-requests/categories/{slug}/edit  // Edit form
PUT    /feature-requests/categories/{slug}       // Update
DELETE /feature-requests/categories/{slug}       // Destroy
```

### API Routes

The package also provides comprehensive API routes:

```php
// Feature Requests API
GET    /api/feature-requests                    // List all
POST   /api/feature-requests                    // Create
GET    /api/feature-requests/{slug}             // Show
PUT    /api/feature-requests/{slug}             // Update
DELETE /api/feature-requests/{slug}             // Delete
PATCH  /api/feature-requests/{slug}/status      // Update status
PATCH  /api/feature-requests/{slug}/assign      // Assign to user
PATCH  /api/feature-requests/{slug}/toggle-featured // Toggle featured

// Voting API
POST   /api/feature-requests/{slug}/vote        // Vote
DELETE /api/feature-requests/{slug}/vote        // Unvote
GET    /api/feature-requests/votes/statistics   // Vote statistics

// Comments API
GET    /api/feature-requests/comments           // List comments
POST   /api/feature-requests/comments           // Create comment
PUT    /api/feature-requests/comments/{id}      // Update comment
DELETE /api/feature-requests/comments/{id}      // Delete comment

// Categories API
GET    /api/feature-requests/categories         // List categories
POST   /api/feature-requests/categories         // Create category
PUT    /api/feature-requests/categories/{slug}  // Update category
DELETE /api/feature-requests/categories/{slug}  // Delete category
```

## 🎨 Frontend Integration

### Vue.js Components

The package includes Vue.js components for easy frontend integration:

```vue
<template>
  <FeatureRequestsIndex />
</template>

<script>
import FeatureRequestsIndex from '@laravelplus/feature-requests/components/FeatureRequestsIndex.vue'

export default {
  components: {
    FeatureRequestsIndex
  }
}
</script>
```

### Blade Views

Use the included Blade views with your existing Laravel application:

```blade
@extends('feature-requests::layouts.app')

@section('content')
    <div class="container">
        <h1>Feature Requests</h1>
        <!-- Your content here -->
    </div>
@endsection
```

## 🔧 Models

### FeatureRequest Model

```php
use LaravelPlus\FeatureRequests\Models\FeatureRequest;

// Scopes
$featureRequests = FeatureRequest::published()->get();
$featureRequests = FeatureRequest::featured()->get();
$featureRequests = FeatureRequest::byStatus('pending')->get();
$featureRequests = FeatureRequest::byPriority('high')->get();
$featureRequests = FeatureRequest::byCategory($categoryId)->get();
$featureRequests = FeatureRequest::mostVoted()->get();
$featureRequests = FeatureRequest::recent()->get();

// Relationships
$featureRequest->user;           // BelongsTo User
$featureRequest->category;       // BelongsTo Category
$featureRequest->votes;          // HasMany Vote
$featureRequest->comments;       // HasMany Comment
```

### Category Model

```php
use LaravelPlus\FeatureRequests\Models\Category;

// Scopes
$categories = Category::active()->get();
$categories = Category::bySlug('user-interface')->get();
$categories = Category::withFeatureRequests()->get();

// Relationships
$category->featureRequests;      // HasMany FeatureRequest
```

### Vote Model

```php
use LaravelPlus\FeatureRequests\Models\Vote;

// Scopes
$votes = Vote::byType('up')->get();
$votes = Vote::byUser($userId)->get();
$votes = Vote::byFeatureRequest($featureRequestId)->get();
$votes = Vote::upVotes()->get();
$votes = Vote::downVotes()->get();

// Relationships
$vote->user;                     // BelongsTo User
$vote->featureRequest;           // BelongsTo FeatureRequest
```

### Comment Model

```php
use LaravelPlus\FeatureRequests\Models\Comment;

// Scopes
$comments = Comment::approved()->get();
$comments = Comment::pinned()->get();
$comments = Comment::byUser($userId)->get();
$comments = Comment::byFeatureRequest($featureRequestId)->get();
$comments = Comment::parentComments()->get();
$comments = Comment::replies()->get();
$comments = Comment::recent()->get();

// Relationships
$comment->user;                  // BelongsTo User
$comment->featureRequest;        // BelongsTo FeatureRequest
$comment->parent;                // BelongsTo Comment (parent)
$comment->replies;               // HasMany Comment (replies)
```

## 🧪 Testing

Run the test suite:

```bash
# Run all tests
./vendor/bin/phpunit

# Run specific test
./vendor/bin/phpunit tests/Unit/Models/FeatureRequestTest.php

# Run with coverage
./vendor/bin/phpunit --coverage-html coverage
```

### Test Structure

```
tests/
├── Unit/
│   ├── Models/
│   │   ├── FeatureRequestTest.php
│   │   ├── CategoryTest.php
│   │   ├── VoteTest.php
│   │   └── CommentTest.php
│   ├── Services/
│   └── Repositories/
├── Feature/
│   ├── FeatureRequestControllerTest.php
│   ├── VoteControllerTest.php
│   ├── CommentControllerTest.php
│   └── CategoryControllerTest.php
└── TestCase.php
```

## 🎨 Customization

### Custom Views

Publish and customize the views:

```bash
php artisan vendor:publish --tag=feature-requests-views
```

### Custom Styling

The package uses Tailwind CSS with shadcn/ui design tokens. You can customize the styling by:

1. Publishing the views
2. Modifying the CSS classes
3. Adding your own custom styles

### Custom Middleware

Add custom middleware in the configuration:

```php
'middleware' => ['web', 'auth', 'custom-middleware'],
```

## 🔒 Security

- **Authentication**: All routes require authentication by default
- **Authorization**: Users can only edit their own feature requests
- **Validation**: Comprehensive input validation
- **CSRF Protection**: All forms include CSRF tokens
- **XSS Protection**: Output is properly escaped

## 📊 Statistics

The package provides built-in statistics:

```php
use LaravelPlus\FeatureRequests\FeatureRequests;

// Get overall statistics
$stats = FeatureRequests::getStatistics();

// Get vote statistics
$voteStats = FeatureRequests::getVoteStatistics();

// Get category statistics
$categoryStats = FeatureRequests::getCategoryStatistics();
```

## 🚀 Performance

- **Eager Loading**: Relationships are properly eager loaded
- **Database Indexing**: Optimized database indexes
- **Caching**: Built-in caching for frequently accessed data
- **Pagination**: Efficient pagination for large datasets

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🆘 Support

- **Documentation**: [GitHub Wiki](https://github.com/LaravelPlus/feature-requests/wiki)
- **Issues**: [GitHub Issues](https://github.com/LaravelPlus/feature-requests/issues)
- **Discussions**: [GitHub Discussions](https://github.com/LaravelPlus/feature-requests/discussions)

## 🙏 Acknowledgments

- Built with [Laravel](https://laravel.com)
- UI inspired by [shadcn/ui](https://ui.shadcn.com)
- Icons by [Lucide](https://lucide.dev)
- Styling with [Tailwind CSS](https://tailwindcss.com)

---

Made with ❤️ by the LaravelPlus team