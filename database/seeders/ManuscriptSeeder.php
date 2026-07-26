<?php

namespace Database\Seeders;

use App\Models\JournalCategory;
use App\Models\Manuscript;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * 10 sample manuscripts, already in the "published" status, so the
 * public portal at /journal/articles has real content to filter by
 * A-Z and category right after a fresh install.
 *
 * Run JournalCategorySeeder before this one so categories exist to
 * attach to (falls back gracefully to "Others" or no category if not).
 */
class ManuscriptSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::firstWhere('email', 'journal.author@example.com');
        $editor = User::firstWhere('email', 'journal.editor-in-chief@example.com');

        if (!$author) {
            $this->command?->warn('No journal.author@example.com found — run RoleDemoUserSeeder first.');

            return;
        }

        $categories = JournalCategory::pluck('id', 'slug');

        $manuscripts = [
            [
                'title' => 'Oral Tradition and Historical Memory Among the Oromo',
                'category' => 'history',
                'keywords' => 'oral history, memory, tradition',
                'abstract' => 'This paper examines how oral tradition preserves historical memory across generations, using a comparative framework drawn from community interviews and archival records.',
            ],
            [
                'title' => 'Gadaa System and Indigenous Governance Structures',
                'category' => 'social-science',
                'keywords' => 'gadaa, governance, indigenous institutions',
                'abstract' => 'An analysis of the Gadaa system as a functioning indigenous governance model, its historical evolution, and its relevance to contemporary institutional design.',
            ],
            [
                'title' => 'Afaan Oromo Poetry: Form, Rhythm, and Meaning',
                'category' => 'literature',
                'keywords' => 'poetry, Afaan Oromo, literary form',
                'abstract' => 'A literary analysis of classical and contemporary Afaan Oromo poetry, focusing on structural conventions and how they carry cultural meaning.',
            ],
            [
                'title' => 'The Long Rains: A Short Story Collection Review',
                'category' => 'fiction',
                'keywords' => 'fiction, short stories, review',
                'abstract' => 'A critical review of a recent short fiction collection exploring migration, land, and belonging in the Horn of Africa.',
            ],
            [
                'title' => 'Soil Degradation and Agricultural Adaptation in the Rift Valley',
                'category' => 'science',
                'keywords' => 'agriculture, soil science, climate adaptation',
                'abstract' => 'A field study of soil degradation patterns and the adaptive farming practices developed by smallholder communities in the Rift Valley region.',
            ],
            [
                'title' => 'Curriculum Reform and Mother-Tongue Instruction',
                'category' => 'education',
                'keywords' => 'education policy, mother-tongue instruction, curriculum',
                'abstract' => 'This study evaluates the outcomes of mother-tongue instruction reforms in primary education and their effect on early literacy rates.',
            ],
            [
                'title' => 'Textile Motifs and Symbolic Meaning in Ceremonial Dress',
                'category' => 'culture',
                'keywords' => 'textiles, symbolism, ceremonial dress',
                'abstract' => 'An ethnographic account of the symbolic vocabulary embedded in ceremonial textile patterns and what they communicate about social status and rite of passage.',
            ],
            [
                'title' => 'Diaspora Networks and Remittance Economies',
                'category' => 'social-science',
                'keywords' => 'diaspora, remittances, economic development',
                'abstract' => 'An examination of how diaspora remittance networks shape local economic development, drawing on household survey data from three regions.',
            ],
            [
                'title' => 'Water Access and Community Cooperative Management',
                'category' => 'science',
                'keywords' => 'water resources, cooperative management, sustainability',
                'abstract' => 'A case study of community-managed water cooperatives and their effectiveness compared to centralized water distribution models.',
            ],
            [
                'title' => 'Reframing the Archive: Digitizing Community Records',
                'category' => 'others',
                'keywords' => 'archives, digitization, community records',
                'abstract' => 'This paper discusses the methodology and ethical considerations involved in digitizing community-held historical records for public research access.',
            ],
        ];

        foreach ($manuscripts as $index => $data) {
            $publishedAt = now()->subDays(90 - ($index * 7));

            Manuscript::updateOrCreate(
                ['title' => $data['title']],
                [
                    'abstract' => $data['abstract'],
                    'keywords' => $data['keywords'],
                    'author_id' => $author->id,
                    'associate_editor_id' => $editor?->id,
                    'category_id' => $categories[$data['category']] ?? null,
                    'status' => 'published',
                    'doi' => '10.5555/ora.'.now()->year.'.'.str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                    'submitted_at' => $publishedAt->copy()->subDays(45),
                    'decided_at' => $publishedAt->copy()->subDays(10),
                    'decided_by' => $editor?->id,
                    'published_at' => $publishedAt,
                    'created_by' => $author->id,
                ]
            );
        }
    }
}
