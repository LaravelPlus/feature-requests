<?php

namespace LaravelPlus\FeatureRequests\Database\Factories;

use LaravelPlus\FeatureRequests\Models\Vote;
use LaravelPlus\FeatureRequests\Models\FeatureRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class VoteFactory extends Factory
{
    protected $model = Vote::class;

    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'feature_request_id' => FeatureRequest::factory(),
            'vote_type' => $this->faker->randomElement(['up', 'down']),
        ];
    }

    public function upVote(): static
    {
        return $this->state(fn (array $attributes) => [
            'vote_type' => 'up',
        ]);
    }

    public function downVote(): static
    {
        return $this->state(fn (array $attributes) => [
            'vote_type' => 'down',
        ]);
    }

    public function forFeatureRequest($featureRequestId): static
    {
        return $this->state(fn (array $attributes) => [
            'feature_request_id' => $featureRequestId,
        ]);
    }

    public function byUser($userId): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $userId,
        ]);
    }
}
