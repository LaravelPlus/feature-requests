<?php

namespace LaravelPlus\FeatureRequests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class FeatureRequest extends Model
{
    use HasFactory, HasSlug, SoftDeletes;

    protected $table = 'feature_requests';

    protected $fillable = [
        'title',
        'description',
        'additional_info',
        'status',
        'priority',
        'category_id',
        'user_id',
        'assigned_to',
        'due_date',
        'estimated_effort',
        'tags',
        'is_public',
        'is_featured',
        'vote_count',
        'up_votes',
        'down_votes',
        'comment_count',
        'view_count',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'is_public' => 'boolean',
        'is_featured' => 'boolean',
        'tags' => 'array',
        'vote_count' => 'integer',
        'up_votes' => 'integer',
        'down_votes' => 'integer',
        'comment_count' => 'integer',
        'view_count' => 'integer',
        'estimated_effort' => 'integer',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'due_date',
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    /**
     * Get the user that created the feature request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }

    /**
     * Get the user assigned to the feature request.
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'assigned_to');
    }

    /**
     * Get the category that the feature request belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the votes for the feature request.
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    /**
     * Get the comments for the feature request.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the attachments for the feature request.
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(config('feature-requests.models.attachment', \App\Models\Attachment::class), 'attachable');
    }

    /**
     * Scope a query to only include public feature requests.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope a query to only include featured feature requests.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope a query to order by vote count.
     */
    public function scopeOrderByVotes($query, $direction = 'desc')
    {
        return $query->orderBy('vote_count', $direction);
    }

    /**
     * Scope a query to order by creation date.
     */
    public function scopeOrderByNewest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope a query to search feature requests.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('tags', 'like', "%{$search}%");
        });
    }

    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return config("feature-requests.statuses.{$this->status}.label", ucfirst($this->status));
    }

    /**
     * Get the status color.
     */
    public function getStatusColorAttribute(): string
    {
        return config("feature-requests.statuses.{$this->status}.color", 'gray');
    }

    /**
     * Get the status description.
     */
    public function getStatusDescriptionAttribute(): string
    {
        return config("feature-requests.statuses.{$this->status}.description", '');
    }

    /**
     * Check if the feature request can be voted on.
     */
    public function canBeVotedOn(): bool
    {
        return config('feature-requests.voting.enabled', true) && 
               in_array($this->status, ['pending', 'under_review', 'planned']);
    }

    /**
     * Check if the feature request can be commented on.
     */
    public function canBeCommentedOn(): bool
    {
        return config('feature-requests.comments.enabled', true);
    }

    /**
     * Increment the view count.
     */
    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    /**
     * Update the vote count.
     */
    public function updateVoteCount(): void
    {
        $upVotes = $this->votes()->where('vote_type', 'up')->count();
        $downVotes = $this->votes()->where('vote_type', 'down')->count();
        $totalVotes = $upVotes + $downVotes;
        
        $this->update([
            'vote_count' => $totalVotes,
            'up_votes' => $upVotes,
            'down_votes' => $downVotes,
        ]);
    }

    /**
     * Update the comment count.
     */
    public function updateCommentCount(): void
    {
        $this->update(['comment_count' => $this->comments()->count()]);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
