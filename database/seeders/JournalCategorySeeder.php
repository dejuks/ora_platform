<?php

namespace Database\Seeders;

use App\Models\JournalCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Starter set of Journal categories. A Journal Manager can add,
 * rename, deactivate, or remove any of these afterwards from
 * journal.categories.* — this seeder only sets sensible defaults so
 * the manuscript submission form and public portal filter aren't
 * empty on a fresh install.
 */
class JournalCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Fiction', 'description' => 'Novels, short stories, and other literary fiction.', 'sort_order' => 1],
            ['name' => 'Literature', 'description' => 'Literary criticism, poetry, and language studies.', 'sort_order' => 2],
            ['name' => 'Science', 'description' => 'Natural and applied sciences.', 'sort_order' => 3],
            ['name' => 'Social Science', 'description' => 'Sociology, economics, political science, and related fields.', 'sort_order' => 4],
            ['name' => 'History', 'description' => 'Historical events, figures, and eras.', 'sort_order' => 5],
            ['name' => 'Education', 'description' => 'Pedagogy, curricula, and educational research.', 'sort_order' => 6],
            ['name' => 'Culture', 'description' => 'Traditions, language, art, and customs.', 'sort_order' => 7],
            ['name' => 'Others', 'description' => 'Anything that doesn\'t fit an existing category.', 'sort_order' => 99],
        ];

        foreach ($categories as $category) {
            $category['slug'] = Str::slug($category['name']);
            $category['is_active'] = true;

            JournalCategory::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
