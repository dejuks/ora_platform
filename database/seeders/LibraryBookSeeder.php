<?php

namespace Database\Seeders;

use App\Models\LibraryBook;
use App\Models\LibraryBookCopy;
use App\Models\LibraryCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * 10 sample physical titles, already 'active' with approved copies,
 * so /library/catalog has real content to browse, search, and filter
 * by category right after a fresh install.
 *
 * Run LibraryCategorySeeder and RoleDemoUserSeeder before this one.
 */
class LibraryBookSeeder extends Seeder
{
    public function run(): void
    {
        $cataloger = User::firstWhere('email', 'library.cataloger@example.com');
        $manager = User::firstWhere('email', 'library.library-manager@example.com');

        if (! $cataloger) {
            $this->command?->warn('No library.cataloger@example.com found — run RoleDemoUserSeeder first.');

            return;
        }

        $categories = LibraryCategory::pluck('id', 'slug');

        $books = [
            [
                'title' => 'The Gadaa System: Governance and Democracy in Oromo Society',
                'author' => 'Dinsa Lepisa',
                'category' => 'oromo-studies',
                'publisher' => 'ORA Press',
                'edition' => '2nd Edition',
                'subject' => 'Indigenous governance, Gadaa system',
                'description' => 'A comprehensive study of the Gadaa system as a functioning indigenous democratic institution, tracing its historical evolution and enduring relevance.',
                'copies' => 3,
            ],
            [
                'title' => 'A History of the Oromo People',
                'author' => 'Mohammed Hassen',
                'category' => 'history',
                'publisher' => 'Finfinne Publishing',
                'edition' => '1st Edition',
                'subject' => 'Oromo history, Horn of Africa',
                'description' => 'A sweeping historical account of the Oromo people from early settlement through the modern era, drawing on oral tradition and archival sources.',
                'copies' => 2,
            ],
            [
                'title' => 'Afaan Oromo Grammar and Composition',
                'author' => 'Tolera Bacha',
                'category' => 'literature-language',
                'publisher' => 'ORA Press',
                'edition' => '3rd Edition',
                'subject' => 'Afaan Oromo, linguistics',
                'description' => 'A structured grammar reference and composition guide for learners and teachers of Afaan Oromo at all levels.',
                'copies' => 4,
            ],
            [
                'title' => 'Land, Labor, and the Political Economy of the Horn of Africa',
                'author' => 'Getachew Kassa',
                'category' => 'social-science',
                'publisher' => 'Rift Valley Academic',
                'edition' => '1st Edition',
                'subject' => 'Political economy, land rights',
                'description' => 'An economic history of land and labor systems across the Horn of Africa, and their lasting effect on regional development.',
                'copies' => 2,
            ],
            [
                'title' => 'Foundations of Mother-Tongue Education',
                'author' => 'Sena Deressa',
                'category' => 'education',
                'publisher' => 'ORA Press',
                'edition' => '2nd Edition',
                'subject' => 'Mother-tongue instruction, literacy',
                'description' => 'A practical and research-grounded guide to designing mother-tongue instruction programs for early-grade literacy.',
                'copies' => 3,
            ],
            [
                'title' => 'Ecology and Agriculture in the Ethiopian Highlands',
                'author' => 'Chaltu Warqineh',
                'category' => 'natural-sciences',
                'publisher' => 'Highland Sciences Press',
                'edition' => '1st Edition',
                'subject' => 'Agroecology, soil science',
                'description' => 'A field-based study of highland agroecology and the adaptive farming practices sustaining smallholder communities.',
                'copies' => 2,
            ],
            [
                'title' => 'Federalism and Governance in Modern Ethiopia',
                'author' => 'Abiy Girma',
                'category' => 'politics-governance',
                'publisher' => 'Finfinne Publishing',
                'edition' => '1st Edition',
                'subject' => 'Federalism, constitutional law',
                'description' => 'An analysis of ethnic federalism as a governance framework, examining its institutional design and ongoing challenges.',
                'copies' => 2,
            ],
            [
                'title' => 'Ceremony, Symbol, and Society: An Ethnography of the Oromo',
                'author' => 'Lensa Girma',
                'category' => 'culture-anthropology',
                'publisher' => 'ORA Press',
                'edition' => '1st Edition',
                'subject' => 'Ethnography, ceremonial life',
                'description' => 'An ethnographic account of ceremonial practice and symbolic meaning across Oromo communities.',
                'copies' => 2,
            ],
            [
                'title' => 'Oromo-English Dictionary and Phrasebook',
                'author' => 'ORA Language Committee',
                'category' => 'reference',
                'publisher' => 'ORA Press',
                'edition' => '4th Edition',
                'subject' => 'Dictionary, reference',
                'description' => 'A practical bilingual dictionary and phrasebook covering everyday and academic vocabulary.',
                'copies' => 5,
            ],
            [
                'title' => 'Diaspora and Belonging: Oromo Communities Abroad',
                'author' => 'Kaba Urgessa',
                'category' => 'social-science',
                'publisher' => 'Rift Valley Academic',
                'edition' => '1st Edition',
                'subject' => 'Diaspora studies, migration',
                'description' => 'A study of diaspora identity, community formation, and transnational ties among Oromo communities abroad.',
                'copies' => 2,
            ],
        ];

        foreach ($books as $index => $data) {
            $book = LibraryBook::updateOrCreate(
                ['title' => $data['title']],
                [
                    'author' => $data['author'],
                    'isbn' => '978-99944-'.str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT).'-'.random_int(100, 999).'-'.random_int(0, 9),
                    'publisher' => $data['publisher'],
                    'publication_year' => now()->year - random_int(1, 12),
                    'edition' => $data['edition'],
                    'call_number' => 'LB'.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                    'subject' => $data['subject'],
                    'category_id' => $categories[$data['category']] ?? null,
                    'description' => $data['description'],
                    'status' => 'active',
                    'cataloged_by' => $cataloger->id,
                    'approved_by' => $manager?->id ?? $cataloger->id,
                    'approved_at' => now()->subDays(30 - $index),
                    'created_by' => $cataloger->id,
                ]
            );

            // Idempotent: only add copies up to the target count, don't
            // duplicate them if this seeder runs again.
            $existingCopies = $book->copies()->count();

            for ($i = $existingCopies; $i < $data['copies']; $i++) {
                LibraryBookCopy::create([
                    'library_book_id' => $book->id,
                    'barcode' => 'LB'.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT).'-C'.($i + 1),
                    'shelf_location' => chr(65 + ($index % 6)).'-'.random_int(1, 12).'-'.($i + 1),
                    'condition' => 'good',
                    'status' => 'available',
                    'tagged_by' => $cataloger->id,
                    'created_by' => $cataloger->id,
                ]);
            }
        }
    }
}
