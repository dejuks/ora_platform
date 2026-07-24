<?php

namespace Database\Seeders;

use App\Models\LibraryMember;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RoleDemoUserSeeder extends Seeder
{
    /**
     * Creates exactly one dedicated test account per role, in every
     * module, so every role in the system can be logged into and
     * tested immediately after seeding — no manual setup needed.
     *
     * All accounts use password "password". Email/username are
     * derived from {module_code}.{role_slug}, e.g.
     * journal.editor-in-chief@example.com / journaleditor-in-chief.
     */
    public function run(): void
    {
        $roles = Role::with('module')->get();

        foreach ($roles as $role) {

            $slugPart = "{$role->module->code}-{$role->slug}";

            $email = "{$role->module->code}.{$role->slug}@example.com";
            $username = Str::limit(Str::slug($slugPart), 50, '');

            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'first_name' => $role->module->name,
                    'last_name' => $role->name,
                    'username' => $username,
                    'password' => Hash::make('password'),
                    'status' => 'Active',
                    'email_verified' => true,
                    'email_verified_at' => now(),
                ]
            );

            $user->moduleRoles()->syncWithoutDetaching([
                $role->id => [
                    'module_id' => $role->module_id,
                    'is_active' => true,
                ],
            ]);

            // The "Member / Student" role can't borrow, hold, or view
            // fines until a Librarian enrolls them with a LibraryMember
            // record (see MemberController). Auto-enroll the demo
            // account so it's testable immediately, same as every
            // other demo role.
            if ($role->module->code === 'library' && $role->slug === 'library-member') {
                LibraryMember::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'membership_no' => 'LM-'.now()->format('y').'-DEMO1',
                        'member_type' => 'student',
                        'max_active_loans' => 5,
                        'status' => 'active',
                        'joined_at' => now(),
                        'created_by' => $user->id,
                    ]
                );
            }
        }
    }
}
