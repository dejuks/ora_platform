<?php

use App\Http\Controllers\Admin\ArticleController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/wiki')->name('admin.wiki.')->middleware(['auth'])->group(function () {

    // TODO: swap 'auth' above for your module-scoped RBAC middleware,
    // e.g. ->middleware(['auth', 'module.permission:wiki.manage'])

    Route::get('articles', [ArticleController::class, 'index'])->name('articles.index');
    Route::get('articles/create', [ArticleController::class, 'create'])->name('articles.create');
    Route::post('articles', [ArticleController::class, 'store'])->name('articles.store');
    Route::get('articles/trashed', [ArticleController::class, 'trashed'])->name('articles.trashed');
    Route::get('articles/{article}/edit', [ArticleController::class, 'edit'])->name('articles.edit');
    Route::put('articles/{article}', [ArticleController::class, 'update'])->name('articles.update');
    Route::delete('articles/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy');

    Route::patch('articles/{article}/protect', [ArticleController::class, 'protect'])->name('articles.protect');
    Route::patch('articles/{article}/unprotect', [ArticleController::class, 'unprotect'])->name('articles.unprotect');

    Route::patch('articles/{article}/restore', [ArticleController::class, 'restore'])
        ->withTrashed()->name('articles.restore');
    Route::delete('articles/{article}/force', [ArticleController::class, 'forceDelete'])
        ->withTrashed()->name('articles.forceDelete');
});

Route::prefix('admin/wiki')->name('admin.wiki.')->middleware(['auth'])->group(function () {

    // TODO: swap 'auth' for your module-scoped RBAC middleware

    Route::get('contact-messages', [\App\Http\Controllers\Admin\ContactMessageController::class, 'index'])->name('contact-messages.index');
    Route::get('contact-messages/{contactMessage}', [\App\Http\Controllers\Admin\ContactMessageController::class, 'show'])->name('contact-messages.show');
    Route::delete('contact-messages/{contactMessage}', [\App\Http\Controllers\Admin\ContactMessageController::class, 'destroy'])->name('contact-messages.destroy');
});
