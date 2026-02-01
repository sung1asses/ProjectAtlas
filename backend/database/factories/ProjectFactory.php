<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->company();
        $slug = Str::slug($title);
        $description = fake()->sentence(16);
        $descriptionRu = fake('ru_RU')->sentence(16);

        return [
            'slug' => $slug,
            'title_translations' => [
                'en' => $title,
                'ru' => $title,
            ],
            'summary_translations' => [
                'en' => $description,
                'ru' => $descriptionRu,
            ],
            'repo_owner' => 'sung1asses',
            'repo_name' => Str::slug($title),
            'default_branch' => 'main',
            'tags' => fake()->randomElements(['laravel', 'vue', 'docker', 'tailwind', 'vite'], 3),
            'is_featured' => fake()->boolean(),
            'is_published' => true,
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}
