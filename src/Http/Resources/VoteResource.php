<?php

namespace LaravelPlus\FeatureRequests\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VoteResource extends JsonResource
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
            'vote_type' => $this->vote_type,
            'comment' => $this->comment,
            'weight' => $this->weight,
            'is_up_vote' => $this->isUpVote(),
            'is_down_vote' => $this->isDownVote(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
