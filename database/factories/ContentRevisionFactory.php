<?php

namespace Database\Factories;

use App\Models\ContentSection;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContentRevisionFactory extends Factory
{
    public function definition(): array
    {
        return ['content_section_id' => ContentSection::factory(), 'action' => 'draft', 'snapshot' => []];
    }
}
