<?php

namespace LaravelPlus\FeatureRequests\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'feature_request_id' => $this->feature_request_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'parent_id' => $this->parent_id,
            'parent' => new CommentResource($this->whenLoaded('parent')),
            'content' => $this->content,
            'is_approved' => $this->is_approved,
            'is_pinned' => $this->is_pinned,
            'is_reply' => $this->isReply(),
            'is_top_level' => $this->isTopLevel(),
            'depth' => $this->depth,
            'replies' => CommentResource::collection($this->whenLoaded('replies')),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
