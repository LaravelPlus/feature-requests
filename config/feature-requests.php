<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Feature Requests Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration options for the Feature Requests
    | package. You can customize various aspects of the feature request system.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Table Names
    |--------------------------------------------------------------------------
    |
    | Here you can customize the table names for the feature requests system.
    | Make sure to update your migrations if you change these values.
    |
    */
    'tables' => [
        'feature_requests' => 'feature_requests',
        'feature_request_votes' => 'feature_request_votes',
        'feature_request_categories' => 'feature_request_categories',
        'feature_request_comments' => 'feature_request_comments',
    ],

    /*
    |--------------------------------------------------------------------------
    | Model Classes
    |--------------------------------------------------------------------------
    |
    | Here you can customize the model classes used by the feature requests
    | system. This allows you to extend the default models with your own.
    |
    */
    'models' => [
        'feature_request' => \LaravelPlus\FeatureRequests\Models\FeatureRequest::class,
        'vote' => \LaravelPlus\FeatureRequests\Models\Vote::class,
        'category' => \LaravelPlus\FeatureRequests\Models\Category::class,
        'comment' => \LaravelPlus\FeatureRequests\Models\Comment::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Status Configuration
    |--------------------------------------------------------------------------
    |
    | Define the available statuses for feature requests and their colors.
    |
    */
    'statuses' => [
        'pending' => [
            'label' => 'Pending Review',
            'color' => 'yellow',
            'description' => 'Feature request is awaiting review',
        ],
        'under_review' => [
            'label' => 'Under Review',
            'color' => 'blue',
            'description' => 'Feature request is being evaluated',
        ],
        'planned' => [
            'label' => 'Planned',
            'color' => 'purple',
            'description' => 'Feature request is planned for development',
        ],
        'in_progress' => [
            'label' => 'In Progress',
            'color' => 'orange',
            'description' => 'Feature request is currently being developed',
        ],
        'completed' => [
            'label' => 'Completed',
            'color' => 'green',
            'description' => 'Feature request has been implemented',
        ],
        'rejected' => [
            'label' => 'Rejected',
            'color' => 'red',
            'description' => 'Feature request has been rejected',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Voting Configuration
    |--------------------------------------------------------------------------
    |
    | Configure voting behavior and limits.
    |
    */
    'voting' => [
        'enabled' => true,
        'allow_multiple_votes' => false,
        'allow_vote_changes' => true,
        'require_authentication' => true,
        'max_votes_per_user' => null, // null = unlimited
    ],

    /*
    |--------------------------------------------------------------------------
    | Comments Configuration
    |--------------------------------------------------------------------------
    |
    | Configure commenting behavior.
    |
    */
    'comments' => [
        'enabled' => true,
        'require_authentication' => true,
        'allow_anonymous_comments' => false,
        'moderation_required' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Categories Configuration
    |--------------------------------------------------------------------------
    |
    | Configure feature request categories.
    |
    */
    'categories' => [
        'enabled' => true,
        'default_category' => 'general',
        'allow_custom_categories' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination Configuration
    |--------------------------------------------------------------------------
    |
    | Configure pagination settings for feature requests.
    |
    */
    'pagination' => [
        'per_page' => 15,
        'max_per_page' => 100,
    ],

    /*
    |--------------------------------------------------------------------------
    | Permissions Configuration
    |--------------------------------------------------------------------------
    |
    | Configure permissions for different actions.
    |
    */
    'permissions' => [
        'create_feature_request' => 'create feature requests',
        'edit_feature_request' => 'edit feature requests',
        'delete_feature_request' => 'delete feature requests',
        'vote_feature_request' => 'vote on feature requests',
        'comment_feature_request' => 'comment on feature requests',
        'manage_feature_requests' => 'manage feature requests',
        'manage_categories' => 'manage feature request categories',
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Configuration
    |--------------------------------------------------------------------------
    |
    | Configure notification settings for feature requests.
    |
    */
    'notifications' => [
        'notify_on_status_change' => true,
        'notify_on_new_comment' => true,
        'notify_on_new_vote' => false,
        'notify_on_high_vote_count' => true,
        'high_vote_threshold' => 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configure caching for feature requests.
    |
    */
    'cache' => [
        'enabled' => true,
        'ttl' => 3600, // 1 hour in seconds
        'tags' => ['feature-requests'],
    ],
];
