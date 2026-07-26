<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * New permission slugs get added here as real enforcement points
     * are built into the code (e.g. wrapped in
     * middleware('module.permission:journal,assign-reviewers')).
     * Which ROLE holds a permission is the dynamic part — that's
     * managed entirely through the Super Admin > Roles UI.
     */
    public function run(): void
    {
        $permissions = [

            // Generic — reusable by any module's admin-type role
            ['slug' => 'manage-users', 'name' => 'Manage Users', 'description' => 'Add, edit, and remove users within a module.'],
            ['slug' => 'manage-roles', 'name' => 'Manage Roles', 'description' => 'Create roles and assign permissions within a module.'],
            ['slug' => 'manage-settings', 'name' => 'Manage Settings', 'description' => 'Configure module-wide settings.'],

            // Journal Management
            ['slug' => 'manage-workflow', 'name' => 'Manage Workflow', 'description' => 'Configure sections, submission policy, and workflow.'],
            ['slug' => 'make-final-decision', 'name' => 'Make Final Decision', 'description' => 'Issue the final accept/reject decision on a manuscript.'],
            ['slug' => 'assign-associate-editors', 'name' => 'Assign Associate Editors', 'description' => 'Assign Associate Editors to a submission.'],
            ['slug' => 'screen-submissions', 'name' => 'Screen Submissions', 'description' => 'Perform initial scope, quality, and plagiarism screening.'],
            ['slug' => 'assign-reviewers', 'name' => 'Assign Reviewers', 'description' => 'Assign reviewers to a manuscript.'],
            ['slug' => 'recommend-decision', 'name' => 'Recommend Decision', 'description' => 'Recommend an editorial decision to the Editor-in-Chief.'],
            ['slug' => 'review-manuscripts', 'name' => 'Review Manuscripts', 'description' => 'Evaluate manuscript content, methodology, ethics, and language.'],
            ['slug' => 'submit-review', 'name' => 'Submit Review', 'description' => 'Submit a structured review report.'],
            ['slug' => 'submit-manuscript', 'name' => 'Submit Manuscript', 'description' => 'Submit a new manuscript and its metadata.'],
            ['slug' => 'upload-revision', 'name' => 'Upload Revision', 'description' => 'Upload a revised version of a manuscript.'],
            ['slug' => 'respond-to-reviewers', 'name' => 'Respond to Reviewers', 'description' => 'Respond to reviewer comments.'],

            // eBook Publishing
            ['slug' => 'screen-manuscripts', 'name' => 'Screen Manuscripts', 'description' => 'Perform initial screening of submitted manuscripts.'],
            ['slug' => 'assign-peer-reviewers', 'name' => 'Assign Peer Reviewers', 'description' => 'Assign peer reviewers to a book manuscript.'],
            ['slug' => 'make-editorial-decision', 'name' => 'Make Editorial Decision', 'description' => 'Accept, request revision, or reject a book manuscript.'],
            ['slug' => 'convert-and-publish-ebook', 'name' => 'Convert & Publish eBook', 'description' => 'Validate files, convert to PDF/EPUB, upload the final eBook, assign ISBN/DOI.'],
            ['slug' => 'manage-ebook-access', 'name' => 'Manage eBook Access', 'description' => 'Set free/monetized access permissions on a published eBook.'],
            ['slug' => 'manage-payments', 'name' => 'Manage Payments', 'description' => 'Manage Book Processing Charges, validate payments, issue invoices, approve fee waivers.'],
            ['slug' => 'approve-final-proof', 'name' => 'Approve Final Proof', 'description' => 'Approve the final proof of a book before publication.'],

            // Library Management
            ['slug' => 'manage-circulation-policy', 'name' => 'Manage Circulation Policy', 'description' => 'Set lending rules, loan periods, and fine policies.'],
            ['slug' => 'approve-acquisitions', 'name' => 'Approve Acquisitions', 'description' => 'Approve new items for the library collection.'],
            ['slug' => 'manage-acquisitions', 'name' => 'Manage Acquisitions', 'description' => 'Identify vendors, place orders, and receive/inspect deliveries for the pending-acquisition queue.'],
            ['slug' => 'manage-digital-collection', 'name' => 'Manage Digital Collection', 'description' => 'Upload and manage digital library resources and metadata.'],
            ['slug' => 'submit-digital-content', 'name' => 'Submit Digital Content', 'description' => 'Prepare and submit ebooks, journal articles, papers, or licensed content packages for the Digital Librarian\'s review and approval.'],
            ['slug' => 'manage-circulation', 'name' => 'Manage Circulation', 'description' => 'Handle lending, returns, holds, renewals, and fines for physical items.'],
            ['slug' => 'catalog-items', 'name' => 'Catalog Items', 'description' => 'Classify and catalog physical materials (DDC/LCC), assign call numbers/barcodes.'],
            ['slug' => 'manage-inventory', 'name' => 'Manage Inventory', 'description' => 'Conduct stocktaking, audits, and item tagging for the physical collection.'],
            ['slug' => 'borrow-items', 'name' => 'Borrow Items', 'description' => 'Search the catalog, borrow/return items, place holds and reservations.'],

            // Oromo Wikipedia
            ['slug' => 'edit-articles', 'name' => 'Edit Articles', 'description' => 'Create and edit wiki articles and upload free-license media.'],
            ['slug' => 'moderate-content', 'name' => 'Moderate Content', 'description' => 'Delete/restore pages, block disruptive users, protect sensitive pages.'],
            ['slug' => 'manage-categories', 'name' => 'Manage Categories', 'description' => 'Create, edit, enable/disable, and remove wiki article categories.'],
            ['slug' => 'manage-wiki-governance', 'name' => 'Manage Wiki Governance', 'description' => 'Promote/demote administrators, rename accounts, oversee governance policy.'],
            ['slug' => 'suppress-revisions', 'name' => 'Suppress Revisions', 'description' => 'Suppress revisions containing private data and view IPs in abuse cases.'],

            // Repository Management
            ['slug' => 'deposit-items', 'name' => 'Deposit Items', 'description' => 'Upload documents/datasets with bibliographic metadata and set access level.'],
            ['slug' => 'curate-metadata', 'name' => 'Curate Metadata', 'description' => 'Validate and enrich metadata quality, verify copyright, apply access controls.'],
            ['slug' => 'review-repository-submissions', 'name' => 'Review Repository Submissions', 'description' => 'Assess academic quality, check for plagiarism, recommend approval or revision.'],
            ['slug' => 'approve-repository-submissions', 'name' => 'Approve Repository Submissions', 'description' => 'Make the final approval decision on repository deposits.'],
            ['slug' => 'manage-repository-access', 'name' => 'Manage Repository Access', 'description' => 'Manage access control policies and generate analytics reports.'],

            // Researchers' Network
            ['slug' => 'manage-network-groups', 'name' => 'Manage Network Groups', 'description' => 'Approve and manage group memberships, moderate group discussions.'],
            ['slug' => 'publish-announcements', 'name' => 'Publish Announcements', 'description' => 'Publish calls for papers, conferences, events, and notifications.'],
            ['slug' => 'manage-network-users', 'name' => 'Manage Network Users', 'description' => 'Manage user registrations, roles, and privacy/security settings for the network.'],
            ['slug' => 'connect-and-collaborate', 'name' => 'Connect & Collaborate', 'description' => 'Maintain a profile, connect with peers, and participate in groups/messaging.'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['slug' => $permission['slug']], $permission);
        }
    }
}