<?php

namespace LaravelPlus\FeatureRequests\Database\Factories;

use LaravelPlus\FeatureRequests\Models\FeatureRequest;
use LaravelPlus\FeatureRequests\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FeatureRequestFactory extends Factory
{
    protected $model = FeatureRequest::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(6);
        
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => $this->faker->paragraphs(3, true),
            'status' => $this->faker->randomElement(['pending', 'in_progress', 'completed', 'rejected']),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
            'category_id' => Category::factory(),
            'user_id' => \App\Models\User::factory(),
            'assigned_to' => null,
            'due_date' => $this->faker->optional(0.3)->dateTimeBetween('now', '+3 months'),
            'estimated_effort' => $this->faker->optional(0.4)->randomElement(['small', 'medium', 'large', 'xlarge']),
            'tags' => $this->faker->optional(0.5)->words(3, false),
            'is_public' => $this->faker->boolean(80),
            'is_featured' => $this->faker->boolean(10),
            'vote_count' => $this->faker->numberBetween(0, 100),
            'comment_count' => $this->faker->numberBetween(0, 20),
            'view_count' => $this->faker->numberBetween(0, 500),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_progress',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
        ]);
    }

    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'high',
        ]);
    }

    public function mediumPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'medium',
        ]);
    }

    public function lowPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'low',
        ]);
    }

    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_public' => true,
        ]);
    }

    public function private(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_public' => false,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    public function withManyVotes(): static
    {
        return $this->state(fn (array $attributes) => [
            'vote_count' => $this->faker->numberBetween(50, 200),
        ]);
    }

    public function withManyComments(): static
    {
        return $this->state(fn (array $attributes) => [
            'comment_count' => $this->faker->numberBetween(10, 50),
        ]);
    }
}
