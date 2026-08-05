# ORA Platform — Physical Library Branches (Locations)

Adds multi-branch support to the Physical Library — Jimma, Adama,
Finfinnee, Shashamane, Bale Robe, Nekemte, and any others the Library
Manager adds — with role-scoped staff access to each.

## What this adds

- **`library_branches`** — full CRUD (name, code, city, region,
  address, phone, email, active flag) under **Library → Branches**,
  gated by `manage-settings` (Library Manager).
- **`library_book_copies.branch_id`** — every physical copy now
  belongs to one branch. Nullable at the DB level (existing copies
  keep working, shown as "Unassigned" until an Inventory Manager
  assigns one); required for any *new* copy going forward.
- **`library_branch_staff`** — lets the Library Manager scope a
  Cataloger / Inventory Manager / Librarian (Physical) / Acquisition
  Officer to one or more specific branches from the branch's Edit
  page. **A staff member with no assignments at all keeps access to
  every branch** — nobody currently working is locked out by this
  shipping.
- `User::canAccessLibraryBranch()` / `accessibleLibraryBranchIds()` —
  Library Manager and Super Admin always bypass branch scoping
  entirely; this is the single source of truth every controller below
  checks against.

## Where branch-awareness shows up

- **Cataloging** (`BookController`): adding a physical copy now
  requires picking a branch (only branches you're allowed to use are
  listed); the copies table on a book's page shows each copy's branch.
- **Inventory / stocktaking** (`copiesIndex`): filterable by branch,
  and a branch-scoped Inventory Manager only ever sees their own
  branch's copies. The status-update form doubles as a branch
  transfer — both the source and destination branch are permission-
  checked.
- **Circulation** (`CirculationController`): checkout, check-in, and
  staff-side renewal are all blocked if you're not scoped to that
  copy's branch. The Circulation Desk list is filterable by branch too.
- **Public catalog** (`PublicController` + `public/index.blade.php` /
  `public/show.blade.php`): visitors can filter the catalog by branch,
  see "N available at this branch," and a book's detail page shows a
  full per-branch availability breakdown.

## How to apply

1. Copy every file in this archive into your `ora_platform` working
   copy, preserving paths. As with the last package, several files
   (`app/Http/Controllers/Library/BookController.php`,
   `CirculationController.php`, `PublicController.php`,
   `app/Models/LibraryBookCopy.php`, `app/Models/User.php`,
   `app/Services/SidebarService.php`, `routes/web.php`,
   `database/seeders/DatabaseSeeder.php`) are edited copies that fully
   replace what's already in your repo.

2. Run the new migrations:
   ```
   php artisan migrate
   ```

3. Seed the initial six branches (safe to re-run — it's
   `firstOrCreate` by branch code):
   ```
   php artisan db:seed --class=LibraryBranchSeeder
   ```

4. From **Library → Branches**, open a branch's Edit page to assign
   specific staff to it, if you want anyone restricted. Leave everyone
   unassigned if you'd rather keep today's "anyone with the role can
   act anywhere" behavior for now and lock it down later branch by
   branch.

5. Existing physical copies will show as "Unassigned" until an
   Inventory Manager opens **Stocktake / Copies** and transfers each
   one to its real branch (the status-update dropdown includes a
   branch selector for exactly this).

## Files in this archive

```
app/Http/Controllers/Library/BookController.php        (edited)
app/Http/Controllers/Library/BranchController.php       (new)
app/Http/Controllers/Library/CirculationController.php  (edited)
app/Http/Controllers/Library/PublicController.php       (edited)
app/Models/LibraryBookCopy.php                           (edited)
app/Models/LibraryBranch.php                             (new)
app/Models/User.php                                      (edited)
app/Services/SidebarService.php                          (edited)
database/migrations/2027_08_04_000100_create_library_branches_table.php
database/migrations/2027_08_04_000200_add_branch_id_to_library_book_copies_table.php
database/migrations/2027_08_04_000300_create_library_branch_staff_table.php
database/seeders/DatabaseSeeder.php                      (edited)
database/seeders/LibraryBranchSeeder.php                 (new)
resources/views/modules/library/books/show.blade.php     (edited)
resources/views/modules/library/branches/create.blade.php (new)
resources/views/modules/library/branches/edit.blade.php   (new)
resources/views/modules/library/branches/index.blade.php  (new)
resources/views/modules/library/circulation/index.blade.php (edited)
resources/views/modules/library/copies/index.blade.php    (edited)
resources/views/modules/library/public/index.blade.php    (edited)
resources/views/modules/library/public/show.blade.php     (edited)
routes/web.php                                            (edited)
```

## Note on this session

I wasn't able to run `php artisan` or boot Laravel in this sandbox (no
PHP binary available), so this was verified by careful reading and a
brace-balance check across every file, not by actually executing it.
Please run `php artisan migrate` and click through cataloging → tagging
a copy → checkout/check-in → the public catalog once locally before
this goes to production.
