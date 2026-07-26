<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedJournalRoles();
        $this->seedEbookRoles();
        $this->seedLibraryRoles();
        $this->seedWikiRoles();
        $this->seedRepositoryRoles();
        $this->seedResearcherNetworkRoles();
    }

    protected function seedJournalRoles(): void
    {
        $this->seedModuleRoles('journal', [
            [
                'name' => 'Admin / Journal Manager',
                'slug' => 'journal-manager',
                'description' => 'Manages user accounts & permissions, configures journal settings, workflow, sections, and submission policies, oversees technical performance.',
                'is_admin_role' => true,
                'permissions' => ['manage-users', 'manage-roles', 'manage-settings', 'manage-workflow'],
            ],
            [
                'name' => 'Editor-in-Chief',
                'slug' => 'editor-in-chief',
                'description' => 'Final decision on all manuscripts, assigns Associate Editors, ensures academic quality and ethical compliance, oversees the publication process.',
                'is_admin_role' => false,
                'permissions' => ['make-final-decision', 'assign-associate-editors'],
            ],
            [
                'name' => 'Associate Editor',
                'slug' => 'associate-editor',
                'description' => 'Initial screening (scope, quality, plagiarism), desk rejects, assigns reviewers, recommends editorial decisions to the Editor-in-Chief.',
                'is_admin_role' => false,
                'permissions' => ['screen-submissions', 'assign-reviewers', 'recommend-decision'],
            ],
            [
                'name' => 'Reviewer',
                'slug' => 'reviewer',
                'description' => 'Reviews manuscript content, methodology, ethics, and language; follows blinded review policy; submits reports on time.',
                'is_admin_role' => false,
                'permissions' => ['review-manuscripts', 'submit-review'],
            ],
            [
                'name' => 'Author',
                'slug' => 'author',
                'description' => 'Submits manuscripts and metadata, uploads revisions, responds to reviewer comments.',
                'is_admin_role' => false,
                'permissions' => ['submit-manuscript', 'upload-revision', 'respond-to-reviewers'],
            ],
        ]);
    }

    protected function seedEbookRoles(): void
    {
        $this->seedModuleRoles('ebook', [
            [
                'name' => 'Book Editor',
                'slug' => 'book-editor',
                'description' => 'Oversees the entire book publishing workflow: screens manuscripts, assigns peer reviewers, makes editorial decisions, communicates with authors.',
                'is_admin_role' => true,
                'permissions' => ['manage-users', 'manage-roles', 'manage-settings', 'screen-manuscripts', 'assign-peer-reviewers', 'make-editorial-decision'],
            ],
            [
                'name' => 'Peer Reviewer',
                'slug' => 'peer-reviewer',
                'description' => 'Subject-matter expert who conducts confidential peer review and recommends accept, minor/major revisions, or reject.',
                'is_admin_role' => false,
                'permissions' => ['review-manuscripts', 'submit-review'],
            ],
            [
                'name' => 'Digital Content Manager',
                'slug' => 'digital-content-manager',
                'description' => 'Validates file quality, converts manuscripts to PDF/EPUB, uploads final eBooks, assigns ISBN/DOI, sets access permissions.',
                'is_admin_role' => false,
                'permissions' => ['convert-and-publish-ebook', 'manage-ebook-access'],
            ],
            [
                'name' => 'Finance & Operations Officer',
                'slug' => 'finance-operations-officer',
                'description' => 'Manages Book Processing Charges, validates payments, issues invoices/receipts, approves or declines fee waivers.',
                'is_admin_role' => false,
                'permissions' => ['manage-payments'],
            ],
            [
                'name' => 'Author / Researcher',
                'slug' => 'ebook-author',
                'description' => 'Prepares and submits manuscripts with metadata, responds to peer-review feedback, approves the final proof.',
                'is_admin_role' => false,
                'permissions' => ['submit-manuscript', 'respond-to-reviewers', 'approve-final-proof'],
            ],
        ]);
    }

    protected function seedLibraryRoles(): void
    {
        $this->seedModuleRoles('library', [
            [
                'name' => 'Library Manager',
                'slug' => 'library-manager',
                'description' => 'Oversees all library operations, staff, and policies for digital and physical collections; approves acquisitions; generates usage reports.',
                'is_admin_role' => true,
                'permissions' => ['manage-users', 'manage-roles', 'manage-settings', 'manage-circulation-policy', 'approve-acquisitions'],
            ],
            [
                'name' => 'Digital Librarian',
                'slug' => 'digital-librarian',
                'description' => 'Uploads and manages digital content, ensures metadata accuracy, organizes digital collections, monitors usage statistics.',
                'is_admin_role' => false,
                'permissions' => ['manage-digital-collection'],
            ],
            [
                'name' => 'Content Uploader',
                'slug' => 'content-uploader',
                'description' => 'Prepares ebooks, articles, and reports for upload, enters basic metadata, and submits content to the Digital Librarian for approval.',
                'is_admin_role' => false,
                'permissions' => ['submit-digital-content'],
            ],
            [
                'name' => 'External Publisher',
                'slug' => 'external-publisher',
                'description' => 'Provides licensed or subscribed digital content packages, uploads content and metadata for review, and updates new editions or issues periodically.',
                'is_admin_role' => false,
                'permissions' => ['submit-digital-content'],
            ],
            [
                'name' => 'Acquisition Officer',
                'slug' => 'acquisition-officer',
                'description' => 'Identifies and orders books/materials, manages vendor relations, receives and inspects deliveries, coordinates with the Cataloger before the Library Manager gives final approval.',
                'is_admin_role' => false,
                'permissions' => ['manage-acquisitions'],
            ],
            [
                'name' => 'Librarian (Physical)',
                'slug' => 'librarian-physical',
                'description' => 'Manages lending and returning of items, handles holds and renewals, collects fines, assists patrons directly.',
                'is_admin_role' => false,
                'permissions' => ['manage-circulation'],
            ],
            [
                'name' => 'Cataloger',
                'slug' => 'cataloger',
                'description' => 'Classifies items using DDC/LCC standards, assigns call numbers and barcodes, maintains catalog accuracy.',
                'is_admin_role' => false,
                'permissions' => ['catalog-items'],
            ],
            [
                'name' => 'Inventory Manager',
                'slug' => 'inventory-manager',
                'description' => 'Conducts stocktaking and audits, manages item tagging (barcode/RFID), tracks inventory accuracy.',
                'is_admin_role' => false,
                'permissions' => ['manage-inventory'],
            ],
            [
                'name' => 'Member / Student',
                'slug' => 'library-member',
                'description' => 'Searches the catalog, borrows and returns items, places holds/reservations, views borrowing history.',
                'is_admin_role' => false,
                'permissions' => ['borrow-items'],
            ],
        ]);
    }

    protected function seedWikiRoles(): void
    {
        $this->seedModuleRoles('wiki', [
            [
                'name' => 'Bureaucrat & Global Steward',
                'slug' => 'bureaucrat',
                'description' => 'Promotes or demotes local administrators, renames user accounts globally, oversees governance policies.',
                'is_admin_role' => true,
                'permissions' => ['manage-users', 'manage-roles', 'manage-settings', 'manage-wiki-governance', 'manage-categories'],
            ],
            [
                'name' => 'Administrator (Sysop)',
                'slug' => 'sysop',
                'description' => 'Deletes/restores pages, blocks vandals and disruptive IPs, protects sensitive pages, closes deletion discussions.',
                'is_admin_role' => false,
                'permissions' => ['moderate-content', 'manage-categories'],
            ],
            [
                'name' => 'Oversighter / CheckUser',
                'slug' => 'oversighter',
                'description' => 'Suppresses revisions containing private data (GDPR compliance) and views IP addresses only in serious abuse cases.',
                'is_admin_role' => false,
                'permissions' => ['suppress-revisions'],
            ],
            [
                'name' => 'Registered Editor',
                'slug' => 'registered-editor',
                'description' => 'Creates new articles, edits existing content, uploads free-license images/media, participates in policy discussions.',
                'is_admin_role' => false,
                'permissions' => ['edit-articles'],
            ],
        ]);
    }

    protected function seedRepositoryRoles(): void
    {
        $this->seedModuleRoles('repository', [
            [
                'name' => 'Repository Administrator',
                'slug' => 'repository-administrator',
                'description' => 'Oversees overall operations and policies; makes the final approval on all submissions; manages access control; generates analytics.',
                'is_admin_role' => true,
                'permissions' => ['manage-users', 'manage-roles', 'manage-settings', 'approve-repository-submissions', 'manage-repository-access'],
            ],
            [
                'name' => 'Repository Curator',
                'slug' => 'repository-curator',
                'description' => 'Validates metadata quality, enriches records with controlled vocabularies, verifies copyright policies, applies access controls.',
                'is_admin_role' => false,
                'permissions' => ['curate-metadata'],
            ],
            [
                'name' => 'Content Reviewer',
                'slug' => 'repository-content-reviewer',
                'description' => 'Assesses academic quality and relevance of a submission, checks for plagiarism, recommends approval or revision.',
                'is_admin_role' => false,
                'permissions' => ['review-repository-submissions'],
            ],
            [
                'name' => 'Researcher / Author',
                'slug' => 'repository-depositor',
                'description' => 'Uploads documents and datasets, provides complete bibliographic metadata (Dublin Core), specifies access level (Open/Restricted).',
                'is_admin_role' => false,
                'permissions' => ['deposit-items'],
            ],
        ]);
    }

    protected function seedResearcherNetworkRoles(): void
    {
        $this->seedModuleRoles('researcher', [
            [
                'name' => 'Platform Administrator',
                'slug' => 'network-platform-administrator',
                'description' => 'Manages user registrations and roles, oversees security and privacy settings, handles system maintenance and updates.',
                'is_admin_role' => true,
                'permissions' => ['manage-users', 'manage-roles', 'manage-settings', 'manage-network-users'],
            ],
            [
                'name' => 'Group Moderator',
                'slug' => 'group-moderator',
                'description' => 'Approves and manages group memberships, moderates group discussions, ensures adherence to community guidelines.',
                'is_admin_role' => false,
                'permissions' => ['manage-network-groups'],
            ],
            [
                'name' => 'Event / Content Manager',
                'slug' => 'event-content-manager',
                'description' => 'Publishes announcements for journal calls, conferences, and events; keeps platform content current; sends notifications.',
                'is_admin_role' => false,
                'permissions' => ['publish-announcements'],
            ],
            [
                'name' => 'Researcher / Member',
                'slug' => 'network-member',
                'description' => 'Creates and maintains a professional profile, connects with peers, participates in messaging, forums, and groups.',
                'is_admin_role' => false,
                'permissions' => ['connect-and-collaborate'],
            ],
        ]);
    }

    /**
     * Shared: look up the module by code and create/update each role,
     * syncing its permission set by slug.
     */
    protected function seedModuleRoles(string $moduleCode, array $roles): void
    {
        $module = Module::where('code', $moduleCode)->first();

        if (! $module) {
            return;
        }

        foreach ($roles as $roleData) {

            $permissionSlugs = $roleData['permissions'];
            unset($roleData['permissions']);

            $role = Role::updateOrCreate(
                ['module_id' => $module->id, 'slug' => $roleData['slug']],
                [
                    'name' => $roleData['name'],
                    'description' => $roleData['description'],
                    'is_admin_role' => $roleData['is_admin_role'],
                    'is_system' => true,
                ]
            );

            $permissionIds = Permission::whereIn('slug', $permissionSlugs)->pluck('id');

            $role->permissions()->sync($permissionIds);
        }
    }
}
