<?php

namespace Database\Seeders;

use App\Models\LibraryCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Starter set of Library categories — used to classify both the
 * physical catalog (LibraryBook::category_id) and, for display
 * purposes, the digital collection (which currently only carries a
 * free-text 'subject' field). A Library Manager / Cataloger /
 * Digital Librarian can add, rename, deactivate, or remove any of
 * these afterwards from library.categories.* — this seeder only sets
 * sensible defaults so the catalog and public portal filter aren't
 * empty on a fresh install.
 */
class LibraryCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Oromo Studies', 'description' => 'Gadaa system, Oromo history, language, and indigenous institutions.', 'sort_order' => 1],
            ['name' => 'History', 'description' => 'Historical events, figures, and eras across the Horn of Africa and beyond.', 'sort_order' => 2],
            ['name' => 'Social Science', 'description' => 'Sociology, economics, political science, and related fields.', 'sort_order' => 3],
            ['name' => 'Literature & Language', 'description' => 'Poetry, fiction, literary criticism, and language studies.', 'sort_order' => 4],
            ['name' => 'Education', 'description' => 'Pedagogy, curricula, and educational research.', 'sort_order' => 5],
            ['name' => 'Natural Sciences', 'description' => 'Biology, agriculture, environmental and physical sciences.', 'sort_order' => 6],
            ['name' => 'Politics & Governance', 'description' => 'Public policy, law, and governance systems.', 'sort_order' => 7],
            ['name' => 'Culture & Anthropology', 'description' => 'Traditions, ethnography, art, and customs.', 'sort_order' => 8],
            ['name' => 'Reference', 'description' => 'Dictionaries, encyclopedias, and general reference works.', 'sort_order' => 9],
            ['name' => 'Others', 'description' => 'Anything that doesn\'t fit an existing category.', 'sort_order' => 99],
        ];

        foreach ($categories as $category) {
            $category['slug'] = Str::slug($category['name']);
            $category['is_active'] = true;

            LibraryCategory::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
