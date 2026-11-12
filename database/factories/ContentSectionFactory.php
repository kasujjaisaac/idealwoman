<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ContentSectionFactory extends Factory
{
    public function definition(): array
    {
        return ['slug' => fake()->unique()->slug(), 'label' => fake()->sentence(3), 'kind' => 'page', 'draft' => [], 'published' => null];
    }
}
