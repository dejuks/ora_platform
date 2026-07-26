<?php

namespace Database\Seeders;

use App\Models\WikiCategory;
use Illuminate\Database\Seeder;

/**
 * Starter set of Wiki categories. A Sysop/Bureaucrat can add, rename,
 * deactivate, or remove any of these afterwards from
 * wiki.categories.* — this seeder only sets sensible defaults so the
 * article form isn't empty on a fresh install.
 */
class WikiCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'History', 'description' => 'Historical events, figures, and eras.', 'sort_order' => 1],
            ['name' => 'Education', 'description' => 'Schools, curricula, and educational topics.', 'sort_order' => 2],
            ['name' => 'Fiction', 'description' => 'Literary and fictional works.', 'sort_order' => 3],
            ['name' => 'Science', 'description' => 'Natural and applied sciences.', 'sort_order' => 4],
            ['name' => 'Geography', 'description' => 'Places, regions, and physical geography.', 'sort_order' => 5],
            ['name' => 'Biography', 'description' => 'Notable people and their lives.', 'sort_order' => 6],
            ['name' => 'Culture', 'description' => 'Traditions, language, art, and customs.', 'sort_order' => 7],
            ['name' => 'Technology', 'description' => 'Technology, computing, and innovation.', 'sort_order' => 8],
            ['name' => 'Politics', 'description' => 'Government, policy, and public affairs.', 'sort_order' => 9],
            ['name' => 'Religion', 'description' => 'Religions, beliefs, and practices.', 'sort_order' => 10],
        ];

        foreach ($categories as $category) {
            WikiCategory::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
