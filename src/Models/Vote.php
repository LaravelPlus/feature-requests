<?php

namespace LaravelPlus\FeatureRequests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vote extends Model
{
    use HasFactory;

    protected $table = 'feature_request_votes';

    protected $fillable = [
        'feature_request_id',
        'user_id',
        'vote_type',
        'comment',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Vote types
     */
    const VOTE_TYPES = [
        'up' => 'up',
        'down' => 'down',
    ];

    /**
     * Get the feature request that the vote belongs to.
     */
    public function featureRequest(): BelongsTo
    {
        return $this->belongsTo(FeatureRequest::class);
    }

    /**
     * Get the user that cast the vote.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }

    /**
     * Scope a query to only include up votes.
     */
    public function scopeUpVotes($query)
    {
        return $query->where('vote_type', 'up');
    }

    /**
     * Scope a query to only include down votes.
     */
    public function scopeDownVotes($query)
    {
        return $query->where('vote_type', 'down');
    }

    /**
     * Check if this is an up vote.
     */
    public function isUpVote(): bool
    {
        return $this->vote_type === 'up';
    }

    /**
     * Check if this is a down vote.
     */
    public function isDownVote(): bool
    {
        return $this->vote_type === 'down';
    }

    /**
     * Get the vote weight (1 for up, -1 for down).
     */
    public function getWeightAttribute(): int
    {
        return $this->isUpVote() ? 1 : -1;
    }
}
