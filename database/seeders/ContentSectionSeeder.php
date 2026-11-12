<?php

namespace Database\Seeders;

use App\Models\ContentSection;
use Illuminate\Database\Seeder;

class ContentSectionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('idealwoman.stories') as $slug => $story) {
            $story['body'] = implode("\n\n", $story['paragraphs']);
            unset($story['paragraphs']);
            ContentSection::query()->firstOrCreate(['slug' => 'story-'.$slug], ['kind' => 'story', 'label' => $story['title'], 'draft' => $story, 'published' => $story, 'published_at' => now()]);
        }
    }
}
