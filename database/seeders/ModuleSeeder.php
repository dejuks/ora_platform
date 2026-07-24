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
                'default_role_slug' => 'author',
            ],
            [
                'name' => 'Ebook',
                'code' => 'ebook',
                'icon' => 'bi-book',
                'default_role_slug' => 'ebook-author',
            ],
            [
                'name' => 'Library Management',
                'code' => 'library',
                'icon' => 'bi-building',
                'default_role_slug' => 'library-member',
            ],
            [
                'name' => 'Researcher Network',
                'code' => 'researcher',
                'icon' => 'bi-people',
                'default_role_slug' => 'network-member',
            ],
            [
                'name' => 'Oromo Wikipedia',
                'code' => 'wiki',
                'icon' => 'bi-globe',
                'default_role_slug' => 'registered-editor',
            ],
            [
                'name' => 'Repository',
                'code' => 'repository',
                'icon' => 'bi-archive',
                'default_role_slug' => 'repository-depositor',
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
                    'is_self_registerable' => true,
                    'default_role_slug' => $module['default_role_slug'],
                ]
            );
        }
    }
}
