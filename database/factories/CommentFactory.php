<?php

namespace LaravelPlus\FeatureRequests\Database\Factories;

use LaravelPlus\FeatureRequests\Models\Comment;
use LaravelPlus\FeatureRequests\Models\FeatureRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'feature_request_id' => FeatureRequest::factory(),
            'content' => $this->faker->paragraphs(2, true),
            'parent_id' => null,
            'is_approved' => $this->faker->boolean(90),
            'is_pinned' => $this->faker->boolean(5),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved' => true,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved' => false,
        ]);
    }

    public function pinned(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_pinned' => true,
        ]);
    }

    public function reply($parentId): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parentId,
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
