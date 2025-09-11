<?php

declare(strict_types=1);

namespace LaravelPlus\FeatureRequests\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeatureRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'status_color' => $this->status_color,
            'status_description' => $this->status_description,
            'priority' => $this->priority,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'user' => new UserResource($this->whenLoaded('user')),
            'assigned_to' => new UserResource($this->whenLoaded('assignedTo')),
            'due_date' => $this->due_date?->toISOString(),
            'estimated_effort' => $this->estimated_effort,
            'tags' => $this->tags,
            'is_public' => $this->is_public,
            'is_featured' => $this->is_featured,
            'vote_count' => $this->vote_count,
            'comment_count' => $this->comment_count,
            'view_count' => $this->view_count,
            'can_be_voted_on' => $this->canBeVotedOn(),
            'can_be_commented_on' => $this->canBeCommentedOn(),
            'votes' => VoteResource::collection($this->whenLoaded('votes')),
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
