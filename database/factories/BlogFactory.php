<?php

namespace Database\Factories;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BlogFactory extends Factory
{
    protected $model = Blog::class;

    public function configure(): static
    {
        return $this->afterCreating(function (Blog $blog) {
            $tags = Tag::factory()->count($this->faker->numberBetween(0, 3))->create();
            $blog->tags()->sync($tags->pluck('id')->all());
        });
    }

    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(6);

        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . $this->faker->unique()->numberBetween(1000, 9999),
            'content' => $this->faker->paragraphs(6, true),
            'excerpt' => $this->faker->optional()->text(200),
            'cover_image' => $this->faker->optional()->imageUrl(1200, 630, 'business', true),
            'category_id' => Category::factory(),
            'status' => $this->faker->randomElement(['draft', 'published']),
            'views' => $this->faker->numberBetween(0, 5000),
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => ['status' => 'published']);
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => 'draft']);
    }
}
