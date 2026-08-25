<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            // Breaking & General News
            'Breaking News',
            'Developing Story',
            'Special Report',
            'Fact Check',
            'Exclusive',
            'Investigation',

            // Politics & Global Affairs
            'Elections 2026',
            'Geopolitics',
            'Public Policy',
            'Diplomacy',
            'Parliament',
            'White House',

            // Technology & Business
            'Artificial Intelligence',
            'Cybersecurity',
            'Startups',
            'Stock Market',
            'Cryptocurrency',
            'Global Economy',
            'Big Tech',

            // Climate & Science
            'Climate Change',
            'Renewable Energy',
            'Space Exploration',
            'Public Health',
            'Biotech',

            // Culture & Society
            'Pop Culture',
            'Entertainment',
            'Higher Education',
            'Social Media',
            'Mental Health',

            // Sports & Events
            'World Cup',
            'Olympics',
            'Transfer News',
            'Match Highlights',
        ];

        foreach ($tags as $tag) {
            Tag::updateOrCreate(
                ['name' => $tag, 'created_by' => 1],
            );
        }
    }
}