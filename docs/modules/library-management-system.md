# ORA Library Management System

The Library module has two parts that share one codebase and one admin
area (`/library`), but have separate actor roles, permissions, and
workflows:

- **Digital Library** — ebooks, journal articles, papers, licensed content
- **Physical Library** — books and other physical items, circulation, holds, fines

Both parts are implemented under `app/Http/Controllers/Library/` and
`app/Models/Library*`, with roles/permissions seeded in
`database/seeders/RoleSeeder.php` (`seedLibraryRoles()`) and
`database/seeders/PermissionSeeder.php`.

---

## 1. Digital Library

### Actor roles & responsibilities

| Actor | Description | Key responsibilities | Role slug | Permission(s) |
|---|---|---|---|---|
| **Digital Librarian** | Manages and maintains all digital library resources | Upload/manage digital resources, ensure metadata accuracy, manage access rights, organize collections, monitor usage, ensure content quality | `digital-librarian` | `manage-digital-collection` |
| **Member / User / Student** | End user searching/reading digital content | Search catalog, read/download digital content | `library-member` | `borrow-items` |
| **Library Manager** *(module admin)* | Oversees the whole module, digital and physical | Create/manage users, assign roles, configure settings, approve acquisitions, generate reports | `library-manager` | `manage-users`, `manage-roles`, `manage-settings`, `manage-circulation-policy`, `approve-acquisitions` |
| **Content Uploader** *(optional)* | Internal staff (e.g. teachers, assistants) submitting content | Upload ebooks/journals/papers, enter metadata, submit for approval | `content-uploader` | `submit-digital-content` |
| **External Publisher** *(optional)* | External providers supplying licensed/subscribed content | Provide licensed content packages, upload + metadata, DRM/licensing compliance, update editions | `external-publisher` | `submit-digital-content` |

### Workflow

**Digital Librarian**
1. Receive/collect digital content for upload.
2. Enter or verify metadata (author, title, subject, year, keywords).
3. Assign access rights based on user type.
4. Upload digital files.
5. Review and approve uploaded content for quality/compliance.
6. Publish content to the digital library portal.
7. Monitor usage statistics and generate reports.

**Member / User / Student**
1. Log in (or access as guest, if allowed).
2. Search digital catalog.
3. Download/read digital content.
4. View borrowing/access history.

**Content Uploader / External Publisher**
1. Prepare content for upload.
2. Enter metadata.
3. Upload files / submit for review.
4. Update metadata or files if the Digital Librarian requests changes.

### Implementation

- Controller: `App\Http\Controllers\Library\DigitalResourceController`
- Model: `App\Models\LibraryDigitalResource`
- Routes (`routes/web.php`, under `library.` prefix):
  - `GET|POST /library/digital-resources` — list / create (gated by `manage-digital-collection` **or** `submit-digital-content`)
  - `POST /library/digital-resources/{resource}/submit-for-review` — Content Uploader / External Publisher hands off a draft to the Digital Librarian
  - `POST /library/digital-resources/{resource}/publish` — Digital Librarian only (`manage-digital-collection`)
  - `POST /library/digital-resources/{resource}/archive` — Digital Librarian only
  - `GET /library/digital-resources/{resource}/download` — Member access

This matches the spec's submit → review → publish chain: a Content
Uploader or External Publisher can create and submit a resource, but
only the Digital Librarian can publish or archive it.

---

## 2. Physical Library

### Actor roles & responsibilities

| Actor | Description | Key responsibilities | Role slug | Permission(s) |
|---|---|---|---|---|
| **Librarian** | Day-to-day circulation, interacts directly with patrons | Lending/returning, holds/reservations/renewals, collect fines, assist patrons, support inventory accuracy | `librarian-physical` | `manage-circulation` |
| **Library Manager** | Oversees operations, staff, policy | Daily operations, acquisitions oversight, circulation policy, staff supervision, reports | `library-manager` | `manage-users`, `manage-roles`, `manage-settings`, `manage-circulation-policy`, `approve-acquisitions` |
| **Acquisition Officer** | Procurement of physical materials | Identify/order books, vendor relations, receive/inspect deliveries, coordinate with Cataloger | `acquisition-officer` | `manage-acquisitions` |
| **Cataloger** | Classifies and catalogs physical materials | Classify (DDC/LCC), assign call numbers/barcodes/RFID, maintain catalog records | `cataloger` | `catalog-items` |
| **Inventory Manager** | Physical inventory and asset management | Shelf reading/stock taking, track missing/damaged items, tagging, periodic audits | `inventory-manager` | `manage-inventory` |
| **Member / Library User** | Patrons who borrow physical materials | Search catalog, borrow/return, holds, renewals, comply with policy | `library-member` | `borrow-items` |
| **System Administrator** | Maintains the platform itself | Manage accounts/permissions, backups, security, system settings | *(platform-level, not a library role — see Platform Administrator)* | — |

### Workflow

**Member / Library User**
1. Search the catalog (OPAC).
2. Place holds/reservations for unavailable items.
3. Borrow available items.
4. Return by due date, or renew.
5. Comply with policy, pay fines if applicable.

**Librarian**
1. Assist members with search/borrow/return/holds/renewals.
2. Check items in/out.
3. Collect fines and fees.
4. Update system records for inventory accuracy.
5. Coordinate with the Library Manager on service issues.

**Library Manager**
1. Oversee daily operations and staffing.
2. Set circulation policy (loan periods, fines, renewals).
3. Supervise Librarian, Acquisition Officer, Cataloger, Inventory Manager.
4. Review/approve acquisition requests.
5. Generate usage and inventory reports.

**Acquisition Officer**
1. Receive purchase requests approved by the Library Manager.
2. Identify suppliers, negotiate, place orders.
3. Inspect deliveries.
4. Coordinate with the Cataloger to process new materials.

**Cataloger**
1. Classify new materials (DDC/LCC).
2. Assign call numbers, barcodes/RFID tags.
3. Create/update catalog records.
4. Work with the Inventory Manager on shelving/accounting.

**Inventory Manager**
1. Conduct shelf reading and stocktaking audits.
2. Track missing/damaged items.
3. Manage tagging.
4. Coordinate periodic audits.

### Implementation

- Controllers:
  - `App\Http\Controllers\Library\BookController` — catalog (`catalog-items`), acquisitions (`approve-acquisitions` / `manage-acquisitions`), copy/inventory management (`manage-inventory`)
  - `App\Http\Controllers\Library\CirculationController` — checkout/checkin/renew (`manage-circulation`)
  - `App\Http\Controllers\Library\HoldController` — holds/reservations
  - `App\Http\Controllers\Library\FineController` — pay/waive fines
  - `App\Http\Controllers\Library\MemberController` — member accounts
  - `App\Http\Controllers\Library\CirculationPolicyController` — loan/fine policy (`manage-circulation-policy`, Library Manager only)
- Models: `LibraryBook`, `LibraryBookCopy`, `LibraryLoan`, `LibraryHold`, `LibraryFine`, `LibraryMember`, `LibraryCirculationPolicy`
- Routes: `routes/web.php`, `library.` prefix (`books`, `copies`, `members`, `circulation`, `holds`, `fines`, `policy`)

Acquisition, cataloging, and inventory are intentionally handled inside
`BookController` rather than three separate controllers — each action
is gated by its own permission (`manage-acquisitions` / `approve-acquisitions`,
`catalog-items`, `manage-inventory`), so the Acquisition Officer,
Cataloger, and Inventory Manager each only see and can do what their
role allows, even though they share one book record.

---

## 3. Shared platform concepts

- **Admin** (per the source spec) maps to the module's own
  `library-manager` role here — the platform already has a per-module
  admin concept (`is_admin_role: true`), so there's no separate global
  "Library Admin" role; Library Manager carries `manage-users`,
  `manage-roles`, and `manage-settings` for the module.
- **System Administrator** (platform-wide accounts, backups, security)
  is a platform-level concern, not module-specific — see the platform's
  Super Admin / System Administrator role rather than a library role.
- Permission checks go through `hasModulePermission('library', <slug>)`,
  defined in `app/Services/SidebarService.php` and enforced per-controller
  via `authorizePermission()`.

## 4. Status

As of this document, every actor role and permission listed above is
seeded (`RoleSeeder::seedLibraryRoles()`, `PermissionSeeder`) and wired
into the controllers/routes described. No gaps were found between this
specification and the current implementation.
