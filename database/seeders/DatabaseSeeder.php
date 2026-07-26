<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ModuleSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            RoleDemoUserSeeder::class,
            WikiCategorySeeder::class,
        ]);

        // Give the "familiar" seeded normal user (johndoe / user@example.com)
        // the Author role too, on top of the dedicated per-role accounts,
        // so there's a login you already know that also has real access.
        $johnDoe = User::where('email', 'user@example.com')->first();

        $authorRole = Role::where('slug', 'author')
            ->whereHas('module', fn ($q) => $q->where('code', 'journal'))
            ->first();

        if ($johnDoe && $authorRole) {
            $johnDoe->moduleRoles()->syncWithoutDetaching([
                $authorRole->id => [
                    'module_id' => $authorRole->module_id,
                    'is_active' => true,
                ],
            ]);
        }
    }
}
