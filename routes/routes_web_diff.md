# routes/web.php changes for Phase 2 + 3

## 1. Remove this import (no longer used):
```php
use App\Http\Controllers\Researcher\RegisterController as ResearcherRegisterController;
```

## 2. Add this import (new controller, Phase 3):
```php
use App\Http\Controllers\ModuleEnrollmentController;
```

## 3. Replace the guest-group registration block:

OLD:
```php
    // Public self-registration — any visitor can create their own
    // Author account and start submitting manuscripts right away.
    Route::get('/register', [RegisterController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [RegisterController::class, 'register'])
        ->name('register.post');

    // Public self-registration — any visitor can create their own
    // account and be enrolled straight into the Researchers'
    // Network as a Member, separate from the Journal-branded
    // /register page above.
    Route::get('/researcher/register', [ResearcherRegisterController::class, 'showRegister'])
        ->name('researcher.register');

    Route::post('/researcher/register', [ResearcherRegisterController::class, 'register'])
        ->name('researcher.register.post');
```

NEW:
```php
    // Public self-registration — one account for the whole platform.
    // The visitor checks which modules to join (Journal, Ebook,
    // Library, Researcher Network, Wiki, Repository) and is enrolled
    // into each immediately with that module's entry-level role.
    Route::get('/register', [RegisterController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [RegisterController::class, 'register'])
        ->name('register.post');

    // Old Researcher-branded signup URL — redirect straight to the
    // unified form so any bookmarked/shared links still work.
    Route::redirect('/researcher/register', '/register')
        ->name('researcher.register');
```

## 4. Add inside the existing `Route::middleware('auth')->group(...)` block
(anywhere after the /dashboard route — e.g. right after it):

```php
    // "My Modules" — self-service page to view what you're enrolled
    // in and join any additional self-registerable module later.
    Route::get('/my-modules', [ModuleEnrollmentController::class, 'index'])
        ->name('my-modules');

    Route::post('/my-modules/{moduleCode}', [ModuleEnrollmentController::class, 'join'])
        ->name('my-modules.join');
```
