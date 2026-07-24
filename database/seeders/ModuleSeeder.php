<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            [
                'name' => 'Journal Management',
                'code' => 'journal',
                'icon' => 'bi-journal-text',
            ],
            [
                'name' => 'Ebook',
                'code' => 'ebook',
                'icon' => 'bi-book',
            ],
            [
                'name' => 'Library Management',
                'code' => 'library',
                'icon' => 'bi-building',
            ],
            [
                'name' => 'Researcher Network',
                'code' => 'researcher',
                'icon' => 'bi-people',
            ],
            [
                'name' => 'Oromo Wikipedia',
                'code' => 'wiki',
                'icon' => 'bi-globe',
            ],
            [
                'name' => 'Repository',
                'code' => 'repository',
                'icon' => 'bi-archive',
            ],
        ];

        foreach ($modules as $module) {

            Module::updateOrCreate(
                ['code' => $module['code']],
                [
                    'name' => $module['name'],
                    'slug' => Str::slug($module['name']),
                    'icon' => $module['icon'],
                    'is_active' => true,
                ]
            );
        }
    }
}