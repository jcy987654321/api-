<?php

namespace Database\Factories;

use App\Models\BlogTag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BlogTagFactory extends Factory
{
    protected $model = BlogTag::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->word();
        
        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
        ];
    }
}
