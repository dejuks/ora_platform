<?php

namespace Database\Seeders;

use App\Models\LibraryDigitalResource;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Sample published digital resources so /library/digital-resources
 * (staff) and the digital section of the public catalog have real,
 * downloadable content right after a fresh install. Each one gets an
 * actual placeholder PDF written to the public disk — without a real
 * file, DigitalResourceController::download() throws a
 * FileNotFoundException instead of a clean 404.
 *
 * Run LibraryCategorySeeder and RoleDemoUserSeeder before this one.
 */
class LibraryDigitalResourceSeeder extends Seeder
{
    /**
     * A tiny, valid single-page PDF — enough for browsers to open
     * and for Storage::download() to serve without erroring. Real
     * uploads replace this the moment someone re-uploads through the
     * UI; this only exists so the demo isn't a broken link.
     */
    protected function placeholderPdf(string $title): string
    {
        $text = 'ORA Digital Library - '.$title;
        $length = strlen($text) + 40;

        return <<<PDF
%PDF-1.4
1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj
2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj
3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 612 792]/Resources<</Font<</F1 4 0 R>>>>/Contents 5 0 R>>endobj
4 0 obj<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>endobj
5 0 obj<</Length {$length}>>
stream
BT /F1 18 Tf 72 720 Td ({$text}) Tj ET
endstream
endobj
trailer<</Size 6/Root 1 0 R>>
%%EOF
PDF;
    }

    public function run(): void
    {
        $digitalLibrarian = User::firstWhere('email', 'library.digital-librarian@example.com');
        $contentUploader = User::firstWhere('email', 'library.content-uploader@example.com');
        $externalPublisher = User::firstWhere('email', 'library.external-publisher@example.com');

        if (! $digitalLibrarian) {
            $this->command?->warn('No library.digital-librarian@example.com found — run RoleDemoUserSeeder first.');

            return;
        }

        $resources = [
            [
                'title' => 'Introduction to Afaan Oromo for Beginners',
                'resource_type' => 'ebook',
                'author' => 'Tolera Bacha',
                'subject' => 'Language learning, Afaan Oromo',
                'keywords' => 'language, beginner, Afaan Oromo',
                'access_level' => 'all_users',
                'uploader' => $digitalLibrarian,
                'description' => 'A beginner-friendly introduction to Afaan Oromo vocabulary, grammar, and everyday conversation.',
            ],
            [
                'title' => 'Curriculum Guide: Mother-Tongue Literacy Toolkit',
                'resource_type' => 'ebook',
                'author' => 'Sena Deressa',
                'subject' => 'Education, literacy',
                'keywords' => 'curriculum, literacy, mother-tongue',
                'access_level' => 'all_users',
                'uploader' => $contentUploader ?? $digitalLibrarian,
                'description' => 'A classroom-ready toolkit for teachers implementing mother-tongue literacy instruction.',
            ],
            [
                'title' => 'Digitized Field Notes: Gadaa Ceremonies 1998–2004',
                'resource_type' => 'paper',
                'author' => 'ORA Research Archive',
                'subject' => 'Ethnography, Gadaa system',
                'keywords' => 'field notes, ceremony, archive',
                'access_level' => 'members_only',
                'uploader' => $digitalLibrarian,
                'description' => 'Digitized primary-source field notes documenting Gadaa ceremonial cycles observed between 1998 and 2004.',
            ],
            [
                'title' => 'Water Cooperative Governance: A Policy Brief',
                'resource_type' => 'paper',
                'author' => 'Rift Valley Policy Institute',
                'subject' => 'Governance, water resources',
                'keywords' => 'policy, water, cooperative governance',
                'access_level' => 'all_users',
                'uploader' => $externalPublisher ?? $digitalLibrarian,
                'description' => 'A policy brief summarizing best practices in community-managed water cooperative governance.',
            ],
            [
                'title' => 'Oral History Interviews: Elders of the Borana',
                'resource_type' => 'other',
                'author' => 'ORA Research Archive',
                'subject' => 'Oral history, Borana',
                'keywords' => 'oral history, interviews, elders',
                'access_level' => 'members_only',
                'uploader' => $digitalLibrarian,
                'description' => 'Transcribed oral history interviews recorded with community elders across Borana zone.',
            ],
            [
                'title' => 'Annual Report on Community Water Access',
                'resource_type' => 'paper',
                'author' => 'Rift Valley Policy Institute',
                'subject' => 'Water access, development',
                'keywords' => 'annual report, water access',
                'access_level' => 'all_users',
                'uploader' => $externalPublisher ?? $digitalLibrarian,
                'description' => 'An annual statistical report tracking community water access initiatives and outcomes.',
            ],
            [
                'title' => 'The Gadaa Calendar: An Illustrated Reference',
                'resource_type' => 'ebook',
                'author' => 'Dinsa Lepisa',
                'subject' => 'Gadaa system, calendar systems',
                'keywords' => 'gadaa, calendar, reference',
                'access_level' => 'staff_only',
                'uploader' => $digitalLibrarian,
                'description' => 'An illustrated internal reference guide to the Gadaa calendar system, for library staff use.',
            ],
        ];

        foreach ($resources as $index => $data) {
            $filePath = "library/digital-resources/seed-{$index}-".now()->timestamp.'.pdf';

            if (! Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->put($filePath, $this->placeholderPdf($data['title']));
            }

            LibraryDigitalResource::updateOrCreate(
                ['title' => $data['title']],
                [
                    'resource_type' => $data['resource_type'],
                    'author' => $data['author'],
                    'description' => $data['description'],
                    'subject' => $data['subject'],
                    'keywords' => $data['keywords'],
                    'access_level' => $data['access_level'],
                    'status' => 'published',
                    'file_path' => $filePath,
                    'file_original_name' => $data['title'].'.pdf',
                    'file_size' => Storage::disk('public')->size($filePath),
                    'mime_type' => 'application/pdf',
                    'uploaded_by' => $data['uploader']->id,
                    'published_by' => $digitalLibrarian->id,
                    'published_at' => now()->subDays(20 - $index),
                    'created_by' => $data['uploader']->id,
                ]
            );
        }
    }
}
