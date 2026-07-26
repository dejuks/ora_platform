<?php

// --- Public journal routes (add inside routes/web.php) ---
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Admin\JournalCategoryController;

Route::prefix('journal')->name('journal.')->group(function () {
    Route::get('articles', [ArticleController::class, 'index'])->name('articles.index');
    Route::get('articles/{article}', [ArticleController::class, 'show'])->name('articles.show');
});

// --- Admin CRUD for categories (add inside your existing admin/auth-protected group) ---
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('journal-categories', JournalCategoryController::class)
        ->except(['show']);
});
