# routes/web.php changes for the Journal category filters

## 1. Delete the stray unmerged file

`routes/journal-routes-to-merge.php` — delete it. It was wired to the
wrong controller/model (`ArticleController` / generic `Article`) and is
fully superseded by the changes below. It was never actually merged
into web.php, so removing it changes nothing at runtime.

## 2. Add an import

Near the other `Journal\...` imports (around line 27):

```php
use App\Http\Controllers\Journal\CategoryController as JournalCategoryController;
```

## 3. Add the category resource route

Inside the existing `journal` admin group — the exact block already in
your file at line 429 — add one line right after the `manuscripts`
resource route:

```php
    Route::prefix('journal')
        ->as('journal.')
        ->middleware('module.access:journal')
        ->group(function () {

            Route::resource('manuscripts', JournalManuscriptController::class)->except('destroy');

            // ADD THIS LINE:
            Route::resource('categories', JournalCategoryController::class)->except('show');

            Route::post('manuscripts/{manuscript}/screen', [JournalManuscriptController::class, 'screen'])
                ->name('manuscripts.screen');
            // ...rest of the file is unchanged
```

This gives you, gated by the same `module.access:journal` middleware
and the `manage-categories` permission check inside the controller:

- GET  /journal/categories            journal.categories.index
- GET  /journal/categories/create     journal.categories.create
- POST /journal/categories            journal.categories.store
- GET  /journal/categories/{c}/edit   journal.categories.edit
- PUT  /journal/categories/{c}         journal.categories.update
- DELETE /journal/categories/{c}       journal.categories.destroy

## 4. Nothing else to change

`journal/articles` (the public portal) is untouched — it's already
routed to `Journal\PublicController` at line 124-130, and the
controller fix + view fix are enough to add the A-Z and category
filters there. No new public route needed.

## 5. Delete these superseded files while you're at it

- app/Http/Controllers/Admin/JournalCategoryController.php  (replaced by app/Http/Controllers/Journal/CategoryController.php)
- app/Http/Controllers/ArticleController.php                (was wired to the wrong model, unused by any real route)
- resources/views/admin/journal-categories/                 (empty directory)
- resources/views/journal/                                  (my earlier generic scaffold view, unused by any real route)
