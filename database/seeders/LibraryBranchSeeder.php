<?php

namespace Database\Seeders;

use App\Models\LibraryBranch;
use Illuminate\Database\Seeder;

class LibraryBranchSeeder extends Seeder
{
    /**
     * The Library's initial physical locations. Idempotent
     * (firstOrCreate by code) so it's safe to re-run alongside real
     * data — the Library Manager can add further branches any time
     * from Library → Branches, this just seeds the starting set.
     */
    public function run(): void
    {
        $branches = [
            ['name' => 'Finfinnee Central Library', 'code' => 'FINFINNEE', 'city' => 'Finfinnee (Addis Ababa)', 'region' => 'Oromia'],
            ['name' => 'Jimma Branch Library', 'code' => 'JIMMA', 'city' => 'Jimma', 'region' => 'Oromia'],
            ['name' => 'Adama Branch Library', 'code' => 'ADAMA', 'city' => 'Adama', 'region' => 'Oromia'],
            ['name' => 'Shashamane Branch Library', 'code' => 'SHASHAMANE', 'city' => 'Shashamane', 'region' => 'Oromia'],
            ['name' => 'Bale Robe Branch Library', 'code' => 'BALEROBE', 'city' => 'Bale Robe', 'region' => 'Oromia'],
            ['name' => 'Nekemte Branch Library', 'code' => 'NEKEMTE', 'city' => 'Nekemte', 'region' => 'Oromia'],
        ];

        foreach ($branches as $branch) {
            LibraryBranch::firstOrCreate(
                ['code' => $branch['code']],
                $branch + ['is_active' => true]
            );
        }
    }
}
