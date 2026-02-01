<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = [
            [
                'title_translations' => [
                    'en' => 'Northwind Logistics',
                    'ru' => 'Northwind Logistics',
                ],
                'slug' => 'supply-chain-ai',
                'summary_translations' => [
                    'en' => 'Data platform redesign with predictive recommendations and fleet visibility.',
                    'ru' => 'Редизайн аналитической платформы с рекомендациями и мониторингом флота.',
                ],
                'repo_owner' => 'sung1asses',
                'repo_name' => 'northwind-logistics',
                'tags' => ['laravel', 'vue', 'docker'],
                'is_featured' => true,
                'sort_order' => 10,
            ],
            [
                'title_translations' => [
                    'en' => 'Beacon Studio',
                    'ru' => 'Beacon Studio',
                ],
                'slug' => 'creator-passport',
                'summary_translations' => [
                    'en' => 'Subscription platform with onboarding flows, identity, and billing automation.',
                    'ru' => 'Подписочная платформа с онбордингом, идентификацией и автоматизацией биллинга.',
                ],
                'repo_owner' => 'sung1asses',
                'repo_name' => 'beacon-studio',
                'tags' => ['laravel', 'tailwind', 'billing'],
                'sort_order' => 20,
            ],
            [
                'title_translations' => [
                    'en' => 'Halo Finance',
                    'ru' => 'Halo Finance',
                ],
                'slug' => 'finops-suite',
                'summary_translations' => [
                    'en' => 'FinOps dashboard consolidating spend, alerts, and compliance workflows.',
                    'ru' => 'Финансовая платформа с дашбордом расходов, алертами и комплаенсом.',
                ],
                'repo_owner' => 'sung1asses',
                'repo_name' => 'halo-finance',
                'tags' => ['laravel', 'vue', 'finops'],
                'sort_order' => 30,
            ],
        ];

        foreach ($projects as $project) {
            Project::updateOrCreate(['slug' => $project['slug']], $project);
        }
    }
}
