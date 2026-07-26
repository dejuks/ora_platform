# Journal Category Filters + A-Z Sort — corrected patch

This replaces the earlier scaffold (which was wired to the wrong model)
with a version built directly against your uploaded `app.zip`. Every
file here was checked against your actual `Manuscript` model,
`Journal\PublicController`, `Journal\ManuscriptController`,
`RoleSeeder`, and the `Wiki\CategoryController` pattern it now mirrors.

## What this does

- Adds a `journal_categories` table + `category_id` on `manuscripts`
- Full CRUD for categories at `/journal/categories` (Journal Manager only,
  gated by the `manage-categories` permission — same pattern as Wiki)
- Authors can pick a category when submitting/editing a manuscript
- The public portal at `/journal/articles` now has:
  - an A–Z bar (filters by first letter of the title)
  - a category sidebar with per-category counts
  - a sort control (A–Z / Z–A / newest)
  - all combinable with the existing search box
- The public portal (index + article page) now shares a real top nav
  with your home page — same brand, same theme colors, and the same
  list of modules pulled live from the `modules` table, not a
  hardcoded copy

## Apply in this order

1. **Delete these superseded files** from your project:
   - `app/Http/Controllers/Admin/JournalCategoryController.php`
   - `app/Http/Controllers/ArticleController.php`
   - `resources/views/admin/journal-categories/` (empty dir)
   - `resources/views/journal/` (my earlier generic view, unused)
   - `routes/journal-routes-to-merge.php`

2. **Copy every file in this zip** into your project at the matching
   path (it overwrites `database/migrations/2026_07_26_..._create_journal_categories_table.php`,
   `app/Models/JournalCategory.php`, `app/Models/Manuscript.php`,
   `app/Http/Controllers/Journal/ManuscriptController.php`, and
   `database/seeders/RoleSeeder.php` with corrected versions — that's
   expected).

3. **Patch routes/web.php** — see `routes/ROUTES_PATCH_INSTRUCTIONS.md`
   inside this zip for the exact 1-line addition + where it goes.

4. Run:
   ```bash
   php artisan migrate
   php artisan db:seed --class=RoleSeeder
   ```
   (safe to re-run — both are idempotent)

5. Seed some default categories (Fiction, Literature, Science, etc.) —
   either add them yourself at `/journal/categories` once logged in as
   Journal Manager, or run this one-off in `php artisan tinker`:
   ```php
   collect(['Fiction','Literature','Science','Social Science','History','Poetry','Others'])
       ->each(fn($n) => \App\Models\JournalCategory::firstOrCreate(
           ['slug' => \Illuminate\Support\Str::slug($n)],
           ['name' => $n, 'is_active' => true]
       ));
   ```

6. Visit `/journal/articles` — you should see the same top nav as your
   home page, plus the A-Z bar and category sidebar.

## Note on the shared nav partial

`resources/views/partials/public-top-nav.blade.php` is written to be
reusable — any other module's public page (`ebook.public.index`,
`wiki.public.index`, `repository.public.index`) can adopt the exact
same nav by adding `@include('partials.public-top-nav', ['active' => 'ebook'])`
(etc.) in place of whatever standalone header it currently has, so the
whole platform's public pages end up visually and navigationally
consistent — not just the Journal one. I only wired it into Journal
since that's what you asked for, but it's a one-line change per page
if you want the same treatment elsewhere.
