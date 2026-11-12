<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EnquiryFactory extends Factory
{
    public function definition(): array
    {
        return ['name' => fake()->name(), 'email' => fake()->safeEmail(), 'enquiry_type' => 'General enquiry', 'message' => fake()->paragraph(), 'status' => 'new'];
    }
}
