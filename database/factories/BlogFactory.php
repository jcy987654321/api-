<?php

namespace Database\Factories;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BlogFactory extends Factory
{
    protected $model = Blog::class;

    public function definition(): array
    {
        $title = $this->faker->sentence(6);
        
        return [
            'title' => $title,
            'slug' => Str::slug($title) . '-' . $this->faker->unique()->numberBetween(1, 9999),
            'content' => $this->faker->paragraphs(5, true),
            'excerpt' => $this->faker->paragraph(2),
            'cover_image' => $this->faker->imageUrl(800, 400, 'blog', true, 'cover'),
            'category_id' => BlogCategory::inRandomOrder()->first()?->id ?? BlogCategory::factory()->create()->id,
            'status' => $this->faker->randomElement(['draft', 'published']),
            'views' => $this->faker->numberBetween(0, 1000),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Blog $blog) {
            $tags = BlogTag::inRandomOrder()->limit(rand(1, 5))->get();
            $blog->tags()->attach($tags);
        });
    }
}
