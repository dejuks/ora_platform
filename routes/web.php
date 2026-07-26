<?php

use App\Http\Controllers\Account\ActivityLogController;
use App\Http\Controllers\Account\ProfileController as AccountProfileController;
use App\Http\Controllers\Account\SettingsController as AccountSettingsController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SystemSettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Journal\PaymentController as JournalPaymentController;
use App\Http\Controllers\Journal\PublicController as JournalPublicController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JoinController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\Ebook\AuthorEnrollmentController as EbookAuthorEnrollmentController;
use App\Http\Controllers\Ebook\BookController as EbookBookController;
use App\Http\Controllers\Ebook\DashboardController as EbookDashboardController;
use App\Http\Controllers\Ebook\PaymentController as EbookPaymentController;
use App\Http\Controllers\Ebook\PublicController as EbookPublicController;
use App\Http\Controllers\Ebook\SettingsController as EbookSettingsController;
use App\Http\Controllers\Ebook\UserController as EbookUserController;
use App\Http\Controllers\Journal\DashboardController as JournalDashboardController;
use App\Http\Controllers\Journal\ManuscriptController as JournalManuscriptController;
use App\Http\Controllers\Journal\SettingsController as JournalSettingsController;
use App\Http\Controllers\Journal\UserController as JournalUserController;
use App\Http\Controllers\Library\BookController as LibraryBookController;
use App\Http\Controllers\Library\CirculationController as LibraryCirculationController;
use App\Http\Controllers\Library\CirculationPolicyController as LibraryCirculationPolicyController;
use App\Http\Controllers\Library\DashboardController as LibraryDashboardController;
use App\Http\Controllers\Library\DigitalResourceController as LibraryDigitalResourceController;
use App\Http\Controllers\Library\FineController as LibraryFineController;
use App\Http\Controllers\Library\HoldController as LibraryHoldController;
use App\Http\Controllers\Library\MemberController as LibraryMemberController;
use App\Http\Controllers\Library\UserController as LibraryUserController;
use App\Http\Controllers\Repository\DashboardController as RepositoryDashboardController;
use App\Http\Controllers\Repository\PublicController as RepositoryPublicController;
use App\Http\Controllers\Repository\RepositoryItemController;
use App\Http\Controllers\Repository\UserController as RepositoryUserController;
use App\Http\Controllers\Researcher\AnnouncementController as ResearcherAnnouncementController;
use App\Http\Controllers\Researcher\ConnectionController as ResearcherConnectionController;
use App\Http\Controllers\Researcher\DashboardController as ResearcherDashboardController;
use App\Http\Controllers\Researcher\GroupController as ResearcherGroupController;
use App\Http\Controllers\Researcher\GroupPostController as ResearcherGroupPostController;
use App\Http\Controllers\Researcher\MessageController as ResearcherMessageController;
use App\Http\Controllers\Researcher\ProfileController as ResearcherProfileController;
use App\Http\Controllers\Researcher\RegisterController as ResearcherRegisterController;
use App\Http\Controllers\Researcher\UserController as ResearcherUserController;
use App\Http\Controllers\Wiki\ArticleController as WikiArticleController;
use App\Http\Controllers\Wiki\ArticleEditRequestController as WikiArticleEditRequestController;
use App\Http\Controllers\Wiki\BlockController as WikiBlockController;
use App\Http\Controllers\Wiki\CategoryController as WikiCategoryController;
use App\Http\Controllers\Wiki\DashboardController as WikiDashboardController;
use App\Http\Controllers\Wiki\DeletionDiscussionController as WikiDeletionDiscussionController;
use App\Http\Controllers\Wiki\PublicController as WikiPublicController;
use App\Http\Controllers\Wiki\RevisionController as WikiRevisionController;
use App\Http\Controllers\Wiki\UserController as WikiUserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC PORTAL (open to everyone — no login required)
|--------------------------------------------------------------------------
|
| The front door of the site: one page listing every active module
| with a link into whichever public area or sign-up flow applies to
| it. Sits outside 'guest'/'auth' on purpose — a logged-in member can
| still come back here, same reasoning as the module portals below.
|
*/

Route::get('/', [PortalController::class, 'index'])->name('portal');

// Both forms live as sections on the portal page itself (#join,
// #contact) — these are just the submit targets, no GET views.
Route::post('/join', [JoinController::class, 'store'])->name('join.store');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

/*
|--------------------------------------------------------------------------
| PUBLIC WIKI PORTAL (open to everyone — no login required)
|--------------------------------------------------------------------------
|
| A published wiki article is public record, same reasoning as the
| Journal / Ebook / Repository public portals below. The controller
| and views already existed but were never wired to a route — added
| here to match that existing pattern.
|
*/

Route::prefix('wiki/articles')
    ->as('wiki.public.')
    ->group(function () {

        Route::get('/', [WikiPublicController::class, 'index'])->name('index');

        Route::get('/random', [WikiPublicController::class, 'random'])->name('random');

        // Literal path before the wildcard article show route below,
        // same reasoning as the manage/articles group — otherwise
        // 'category' would be swallowed as an article slug.
        Route::get('/category/{category:slug}', [WikiPublicController::class, 'category'])->name('category');

        Route::get('/{article}', [WikiPublicController::class, 'show'])->name('show');
    });

Route::get('wiki/about', [WikiPublicController::class, 'about'])->name('wiki.public.about');

/*
|--------------------------------------------------------------------------
| PUBLIC JOURNAL PORTAL (open to everyone — no login required)
|--------------------------------------------------------------------------
|
| A published manuscript is public record: anyone visiting the site
| can browse and read it, logged in or not. This sits outside both
| the 'guest' and 'auth' middleware groups on purpose — it must stay
| reachable no matter who's viewing it.
|
*/

Route::prefix('journal/articles')
    ->as('journal.public.')
    ->group(function () {

        Route::get('/', [JournalPublicController::class, 'index'])->name('index');

        Route::get('/{manuscript}', [JournalPublicController::class, 'show'])->name('show');
    });

/*
|--------------------------------------------------------------------------
| PUBLIC EBOOK PORTAL (open to everyone — no login required)
|--------------------------------------------------------------------------
|
| A published eBook is public record — Open Access titles can be
| browsed and downloaded by anyone, Restricted titles are browsable
| but gate the actual download behind login inside the controller,
| and Embargoed titles are hidden entirely until the embargo lifts.
| Sits outside 'guest'/'auth' on purpose, same reasoning as Journal's
| public portal above.
|
*/

Route::prefix('ebook/library')
    ->as('ebook.public.')
    ->group(function () {

        Route::get('/', [EbookPublicController::class, 'index'])->name('index');

        Route::get('/{book}', [EbookPublicController::class, 'show'])->name('show');
    });

// Download sits outside the public prefix (it's also linked from the
// authenticated book workflow) but stays unauthenticated at the route
// level — access rights are enforced inside the controller itself
// (open/restricted/embargoed).
Route::get('ebook/books/{book}/download', [EbookBookController::class, 'download'])
    ->name('ebook.books.download');

/*
|--------------------------------------------------------------------------
| PUBLIC REPOSITORY PORTAL (open to everyone — no login required)
|--------------------------------------------------------------------------
|
| A published repository item is public scholarly record: anyone can
| browse and cite it, logged in or not — Open Access items can also
| be downloaded by anyone here. Restricted items are browsable (so
| they remain discoverable and citable) but gate the actual download
| behind login inside the controller, and an active embargo hides an
| item entirely until it lifts. Sits outside 'guest'/'auth' on purpose,
| same reasoning as Journal's and Ebook's public portals above.
|
*/

Route::prefix('repository/records')
    ->as('repository.public.')
    ->group(function () {

        Route::get('/', [RepositoryPublicController::class, 'index'])->name('index');

        Route::get('/{item}', [RepositoryPublicController::class, 'show'])->name('show');
    });

// Download sits outside the public prefix (it's also linked from the
// authenticated item workflow) but stays unauthenticated at the route
// level — access rights (open/restricted/embargoed) are enforced
// inside the controller itself, same pattern as the Ebook download
// route above.
Route::get('repository/items/{item}/download', [RepositoryItemController::class, 'download'])
    ->name('repository.items.download');

/*
|--------------------------------------------------------------------------
| CHAPA WEBHOOK (server-to-server, no session/auth, CSRF-exempt)
|--------------------------------------------------------------------------
|
| Chapa calls this directly from its own servers to confirm a payment
| — there's no logged-in user or CSRF token on this request. It is
| explicitly excluded from CSRF verification in bootstrap/app.php.
|
*/

Route::post('journal/payments/chapa/webhook', [JournalPaymentController::class, 'webhook'])
    ->name('journal.payments.chapa.webhook');

Route::post('ebook/payments/chapa/webhook', [EbookPaymentController::class, 'webhook'])
    ->name('ebook.payments.chapa.webhook');

/*
|--------------------------------------------------------------------------
| GUEST ROUTES (not logged in users)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    // Login page (use ONE route only). '/' is now the public portal
    // (see above) and open to everyone, so this named route — the
    // one auth middleware redirects guests to — lives at /login.
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.post');

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

    // Public self-registration — any visitor can create their own
    // account and be enrolled straight into the Researchers'
    // Network as a Member, separate from the Journal-branded
    // /register page above.
    Route::get('/researcher/register', [ResearcherRegisterController::class, 'showRegister'])
        ->name('researcher.register');

    Route::post('/researcher/register', [ResearcherRegisterController::class, 'register'])
        ->name('researcher.register.post');
});

/*
|--------------------------------------------------------------------------
| AUTH ROUTES (logged in users only)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Deliberately NOT behind 'verified' — an unverified user still
    // needs a way off the "verify your email" holding page.
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])
        ->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware('signed')
        ->name('verification.verify');

    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // Smart dashboard: sends Super Admin to the control panel, sends a
    // module admin/member to their module, otherwise a no-access page.
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | ACCOUNT — My Profile, Settings, Activity Log
    |--------------------------------------------------------------------------
    |
    | Available to every logged-in user regardless of module, via the
    | account dropdown in the top-right of the header.
    |
    */

    Route::prefix('account')
        ->as('account.')
        ->group(function () {

            Route::get('profile', [AccountProfileController::class, 'edit'])
                ->name('profile.edit');

            Route::put('profile', [AccountProfileController::class, 'update'])
                ->name('profile.update');

            Route::post('profile/photo', [AccountProfileController::class, 'updatePhoto'])
                ->name('profile.photo');

            Route::put('profile/password', [AccountProfileController::class, 'updatePassword'])
                ->name('profile.password');

            Route::get('settings', [AccountSettingsController::class, 'edit'])
                ->name('settings.edit');

            Route::put('settings', [AccountSettingsController::class, 'update'])
                ->name('settings.update');

            Route::get('activity-log', [ActivityLogController::class, 'index'])
                ->name('activity.index');
        });

    /*
    |--------------------------------------------------------------------------
    | NOTIFICATIONS — in-app notification bell + full list
    |--------------------------------------------------------------------------
    */

    Route::prefix('notifications')
        ->as('notifications.')
        ->group(function () {

            Route::get('/', [NotificationController::class, 'index'])
                ->name('index');

            Route::get('{notification}/open', [NotificationController::class, 'open'])
                ->name('open');

            Route::post('{notification}/read', [NotificationController::class, 'markRead'])
                ->name('read');

            Route::post('mark-all-read', [NotificationController::class, 'markAllRead'])
                ->name('mark-all-read');
        });

    /*
    |--------------------------------------------------------------------------
    | SUPER ADMIN
    |--------------------------------------------------------------------------
    |
    | Manages every module and every user in the system. This is the
    | only place users, modules, and module-admin assignments are
    | created, edited, or disabled from.
    |
    */

    Route::prefix('admin')
        ->as('admin.')
        ->middleware('super_admin')
        ->group(function () {

            Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])
                ->name('dashboard');

            Route::resource('users', UserController::class);

            Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
                ->name('users.toggle-status');

            Route::resource('modules', ModuleController::class);

            Route::patch('modules/{module}/toggle-status', [ModuleController::class, 'toggleStatus'])
                ->name('modules.toggle-status');

            // Dynamic RBAC — create/edit roles per module and choose
            // which permissions each one carries. No code changes
            // needed to define a new role or reassign permissions.
            Route::resource('roles', RoleController::class);

            Route::resource('permissions', PermissionController::class);

            Route::get('settings', [SystemSettingController::class, 'edit'])
                ->name('settings.edit');

            Route::put('settings', [SystemSettingController::class, 'update'])
                ->name('settings.update');
        });

    /*
    |--------------------------------------------------------------------------
    | MODULES
    |--------------------------------------------------------------------------
    |
    | Each module has its own area (members only, via module.access) and
    | its own admin sub-area (that module's admin only, via module.admin).
    | Super Admin passes both checks automatically for every module.
    |
    | NOTE: five modules are wired up below (Journal, Ebook, Library,
    | Researcher Network, Oromo Wikipedia) matching what's been named so
    | far. Add a sixth entry here the same way once it's decided.
    |
    */

    $oraModules = [
        'journal' => [JournalDashboardController::class, JournalUserController::class],
        'ebook' => [EbookDashboardController::class, EbookUserController::class],
        'library' => [LibraryDashboardController::class, LibraryUserController::class],
        'researcher' => [ResearcherDashboardController::class, ResearcherUserController::class],
        'wiki' => [WikiDashboardController::class, WikiUserController::class],
        'repository' => [RepositoryDashboardController::class, RepositoryUserController::class],
    ];

    foreach ($oraModules as $code => [$dashboardController, $userController]) {

        Route::prefix($code)
            ->as("{$code}.")
            ->middleware("module.access:{$code}")
            ->group(function () use ($dashboardController, $userController, $code) {

                Route::get('/', [$dashboardController, 'index'])->name('dashboard');

                Route::prefix('admin')
                    ->as('admin.')
                    ->middleware("module.admin:{$code}")
                    ->group(function () use ($dashboardController, $userController) {

                        Route::get('/', [$dashboardController, 'admin'])->name('dashboard');

                        // A module admin's own scoped user management —
                        // full CRUD, but only ever affects users inside
                        // this module.
                        Route::resource('users', $userController);
                    });
            });
    }

    /*
    |--------------------------------------------------------------------------
    | JOURNAL MANAGEMENT — the manuscript workflow
    |--------------------------------------------------------------------------
    |
    | Submit -> Screen -> Assign Reviewers -> Review -> Recommend ->
    | Decide -> Publish. Every action is gated by the actual permission
    | that role carries (see RoleSeeder), enforced inside the
    | controller itself, not just by hiding buttons in the view.
    |
    */

    Route::prefix('journal')
        ->as('journal.')
        ->middleware('module.access:journal')
        ->group(function () {

            Route::resource('manuscripts', JournalManuscriptController::class)->except('destroy');

            Route::post('manuscripts/{manuscript}/screen', [JournalManuscriptController::class, 'screen'])
                ->name('manuscripts.screen');

            Route::post('manuscripts/{manuscript}/assign-reviewers', [JournalManuscriptController::class, 'assignReviewers'])
                ->name('manuscripts.assign-reviewers');

            Route::post('manuscripts/{manuscript}/reviews/{review}/submit', [JournalManuscriptController::class, 'submitReview'])
                ->name('manuscripts.reviews.submit');

            Route::post('manuscripts/{manuscript}/recommend', [JournalManuscriptController::class, 'recommend'])
                ->name('manuscripts.recommend');

            Route::post('manuscripts/{manuscript}/decide', [JournalManuscriptController::class, 'decide'])
                ->name('manuscripts.decide');

            Route::post('manuscripts/{manuscript}/publish', [JournalManuscriptController::class, 'publish'])
                ->name('manuscripts.publish');

            // Article Processing Charge: the corresponding author pays
            // here once their manuscript is accepted, before it can
            // be published.
            Route::get('manuscripts/{manuscript}/pay', [JournalPaymentController::class, 'show'])
                ->name('manuscripts.pay');

            Route::post('manuscripts/{manuscript}/pay', [JournalPaymentController::class, 'process'])
                ->name('manuscripts.pay.process');

            // Browser lands here after Chapa's checkout page (UX only —
            // the webhook above is the source of truth).
            Route::get('manuscripts/{manuscript}/pay/return', [JournalPaymentController::class, 'returnFromChapa'])
                ->name('manuscripts.pay.return');

            // Journal Manager: configure the Article Processing Charge
            // amount/currency applied whenever a manuscript is accepted.
            Route::get('settings', [JournalSettingsController::class, 'edit'])
                ->name('settings.edit');

            Route::put('settings', [JournalSettingsController::class, 'update'])
                ->name('settings.update');
        });

    /*
    |--------------------------------------------------------------------------
    | OROMO WIKIPEDIA — the editing workflow
    |--------------------------------------------------------------------------
    |
    | Registered Editor creates/edits an article (edit-articles) -> any
    | member can nominate it for deletion / weigh in on that discussion
    | -> Administrator (Sysop) protects/deletes/restores pages and
    | blocks disruptive users (moderate-content) -> Oversighter
    | suppresses revisions containing private data (suppress-revisions).
    | Controllers and views already existed but were never wired to a
    | route — added here to match the Journal/Ebook/Library pattern.
    |
    */

    Route::prefix('wiki')
        ->as('wiki.')
        ->middleware('module.access:wiki')
        ->group(function () {

            // NOTE: path is 'wiki/manage/articles', not 'wiki/articles' —
            // the latter is already taken by the public wiki portal
            // above (registered outside auth) and would otherwise
            // shadow every route in this group. Route *names* still
            // read as wiki.articles.* to match the views/controllers.
            Route::prefix('manage/articles')
                ->as('articles.')
                ->group(function () {

                    Route::get('/', [WikiArticleController::class, 'index'])->name('index');

                    // Literal 'edit-requests' must come before the '{article}'
                    // wildcard show route below for the same reason 'create'
                    // does — otherwise it gets swallowed as an article ID.
                    Route::get('edit-requests', [WikiArticleEditRequestController::class, 'index'])
                        ->name('edit-requests.index');

                    // A blocked user/IP can still browse the wiki (same as
                    // Wikipedia) but every action that creates, edits, or
                    // otherwise changes content is gated behind an active-
                    // block check.
                    //
                    // NOTE: literal segments ('create') must be registered
                    // before the wildcard '{article}' routes below, or
                    // Laravel matches '{article}' first and tries to
                    // route-model-bind the literal string "create" as an
                    // article ID.
                    Route::middleware('wiki.not_blocked')->group(function () {
                        Route::get('create', [WikiArticleController::class, 'create'])->name('create');
                        Route::post('/', [WikiArticleController::class, 'store'])->name('store');
                    });

                    Route::get('{article}', [WikiArticleController::class, 'show'])->name('show');

                    Route::middleware('wiki.not_blocked')->group(function () {
                        Route::get('{article}/edit', [WikiArticleController::class, 'edit'])->name('edit');
                        Route::put('{article}', [WikiArticleController::class, 'update'])->name('update');

                        // Owner-approval editing workflow: anyone can ask,
                        // only the owner (or a Sysop/Bureaucrat) decides.
                        Route::post('{article}/edit-requests', [WikiArticleEditRequestController::class, 'store'])
                            ->name('edit-requests.store');
                        Route::post('{article}/edit-requests/{editRequest}/approve', [WikiArticleEditRequestController::class, 'approve'])
                            ->name('edit-requests.approve');
                        Route::post('{article}/edit-requests/{editRequest}/reject', [WikiArticleEditRequestController::class, 'reject'])
                            ->name('edit-requests.reject');

                        // Administrator (Sysop) — the protect/restore forms
                        // submit plain POST (no @method spoofing), hence POST here.
                        Route::post('{article}/protect', [WikiArticleController::class, 'protect'])->name('protect');
                        Route::delete('{article}', [WikiArticleController::class, 'destroy'])->name('destroy');
                        Route::post('{article}/restore', [WikiArticleController::class, 'restore'])
                            ->withTrashed()->name('restore');

                        // Nominate this article for deletion (Articles for Deletion)
                        Route::post('{article}/deletions', [WikiDeletionDiscussionController::class, 'store'])
                            ->name('deletions.store');
                    });
                });

            Route::prefix('deletions')
                ->as('deletions.')
                ->group(function () {

                    Route::get('/', [WikiDeletionDiscussionController::class, 'index'])->name('index');
                    Route::get('{discussion}', [WikiDeletionDiscussionController::class, 'show'])->name('show');

                    Route::middleware('wiki.not_blocked')->group(function () {
                        Route::post('{discussion}/comment', [WikiDeletionDiscussionController::class, 'comment'])->name('comment');

                        // Administrator (Sysop)
                        Route::post('{discussion}/close', [WikiDeletionDiscussionController::class, 'close'])->name('close');
                    });
                });

            // Administrator (Sysop) / Bureaucrat: configure categories
            Route::prefix('categories')
                ->as('categories.')
                ->middleware('wiki.not_blocked')
                ->group(function () {

                    Route::get('/', [WikiCategoryController::class, 'index'])->name('index');
                    Route::get('create', [WikiCategoryController::class, 'create'])->name('create');
                    Route::post('/', [WikiCategoryController::class, 'store'])->name('store');
                    Route::get('{category}/edit', [WikiCategoryController::class, 'edit'])->name('edit');
                    Route::put('{category}', [WikiCategoryController::class, 'update'])->name('update');
                    Route::delete('{category}', [WikiCategoryController::class, 'destroy'])->name('destroy');
                });

            // Administrator (Sysop): block disruptive users/IPs
            Route::prefix('blocks')
                ->as('blocks.')
                ->group(function () {

                    Route::get('/', [WikiBlockController::class, 'index'])->name('index');
                    Route::get('create', [WikiBlockController::class, 'create'])->name('create');
                    Route::post('/', [WikiBlockController::class, 'store'])->name('store');
                    Route::post('{block}/lift', [WikiBlockController::class, 'lift'])->name('lift');
                });

            // Oversighter/CheckUser: suppress revisions containing private data
            Route::prefix('revisions')
                ->as('revisions.')
                ->group(function () {

                    Route::get('/', [WikiRevisionController::class, 'index'])->name('index');
                    Route::post('{revision}/suppress', [WikiRevisionController::class, 'suppress'])->name('suppress');
                    Route::post('{revision}/unsuppress', [WikiRevisionController::class, 'unsuppress'])->name('unsuppress');
                });
        });

    // Any logged-in ORA user can self-enroll as an eBook Author —
    // deliberately OUTSIDE module.access:ebook below, since a user
    // must be able to request ebook access before they have it.
    Route::post('ebook/become-author', [EbookAuthorEnrollmentController::class, 'enroll'])
        ->name('ebook.become-author');

    /*
    |--------------------------------------------------------------------------
    | EBOOK PUBLISHING — the book workflow
    |--------------------------------------------------------------------------
    |
    | Submit -> Screen -> Assign Peer Reviewers -> Review -> Editorial
    | Decision -> (pay or waive the Book Processing Charge) ->
    | Financial Clearance -> Digital Production (convert, ISBN/DOI,
    | access rights) -> Publish. Every action is gated by the actual
    | permission that role carries (see RoleSeeder).
    |
    */

    Route::prefix('ebook')
        ->as('ebook.')
        ->middleware('module.access:ebook')
        ->group(function () {

            Route::resource('books', EbookBookController::class)
                ->only(['index', 'create', 'store', 'show', 'edit', 'update']);

            Route::post('books/{book}/screen', [EbookBookController::class, 'screen'])
                ->name('books.screen');

            Route::post('books/{book}/assign-reviewers', [EbookBookController::class, 'assignReviewers'])
                ->name('books.assign-reviewers');

            Route::post('books/{book}/reviews/{review}/submit', [EbookBookController::class, 'submitReview'])
                ->name('books.reviews.submit');

            Route::post('books/{book}/decide', [EbookBookController::class, 'decide'])
                ->name('books.decide');

            // Book Processing Charge: the author pays here, or asks
            // the Finance & Operations Officer for a fee waiver.
            Route::get('books/{book}/pay', [EbookPaymentController::class, 'show'])
                ->name('books.pay');

            Route::post('books/{book}/pay', [EbookPaymentController::class, 'process'])
                ->name('books.pay.process');

            Route::get('books/{book}/pay/return', [EbookPaymentController::class, 'returnFromChapa'])
                ->name('books.pay.return');

            Route::post('books/{book}/waiver', [EbookBookController::class, 'requestWaiver'])
                ->name('books.waiver');

            // Finance & Operations Officer: approve/decline waiver,
            // or grant clearance once the fee has landed.
            Route::post('books/{book}/clear', [EbookBookController::class, 'clear'])
                ->name('books.clear');

            // Digital Content Manager: convert + assign ISBN/DOI +
            // set access rights + publish.
            Route::post('books/{book}/publish', [EbookBookController::class, 'publish'])
                ->name('books.publish');

            Route::post('books/{book}/access', [EbookBookController::class, 'updateAccess'])
                ->name('books.access');

            // Book Editor: configure the Book Processing Charge
            // amount/currency applied whenever a book is accepted.
            Route::get('settings', [EbookSettingsController::class, 'edit'])
                ->name('settings.edit');

            Route::put('settings', [EbookSettingsController::class, 'update'])
                ->name('settings.update');
        });

    /*
    |--------------------------------------------------------------------------
    | REPOSITORY MANAGEMENT — the bibliographic deposit workflow
    |--------------------------------------------------------------------------
    |
    | Deposit -> Metadata Validation & Enrichment (Curator) ->
    | Content & Citation Review (Content Reviewer) -> Final Approval
    | (Repository Administrator) -> Publish (persistent URL assigned).
    | Every action is gated by the actual permission that role carries
    | (see RoleSeeder), enforced inside the controller itself, not
    | just by hiding buttons in the view.
    |
    */

    Route::prefix('repository')
        ->as('repository.')
        ->middleware('module.access:repository')
        ->group(function () {

            Route::resource('items', RepositoryItemController::class)->except('destroy');

            Route::post('items/{item}/curate', [RepositoryItemController::class, 'curate'])
                ->name('items.curate');

            Route::post('items/{item}/review', [RepositoryItemController::class, 'review'])
                ->name('items.review');

            Route::post('items/{item}/decide', [RepositoryItemController::class, 'decide'])
                ->name('items.decide');

            Route::post('items/{item}/publish', [RepositoryItemController::class, 'publish'])
                ->name('items.publish');

            // Repository Administrator: adjust access controls
            // (open/restricted, embargo) on a published item.
            Route::post('items/{item}/access', [RepositoryItemController::class, 'updateAccess'])
                ->name('items.access');
        });

    /*
    |--------------------------------------------------------------------------
    | LIBRARY MANAGEMENT — physical circulation
    |--------------------------------------------------------------------------
    |
    | Catalog (Cataloger: catalog-items) -> Acquisition approval
    | (Library Manager: approve-acquisitions) -> copies tagged &
    | tracked (Inventory Manager: manage-inventory) -> checkout /
    | return / renew / holds / fines (Librarian: manage-circulation).
    | Loan period, renewal limit, and fine rate are set by the
    | Library Manager (manage-circulation-policy). Every Member
    | (borrow-items) can browse the catalog, place holds, and view
    | their own loans/fines.
    |
    */

    Route::prefix('library')
        ->as('library.')
        ->middleware('module.access:library')
        ->group(function () {

            Route::resource('books', LibraryBookController::class)->except('destroy');

            Route::get('copies', [LibraryBookController::class, 'copiesIndex'])->name('copies.index');

            Route::post('books/{book}/approve-acquisition', [LibraryBookController::class, 'approveAcquisition'])
                ->name('books.approve-acquisition');

            Route::post('books/{book}/copies', [LibraryBookController::class, 'storeCopy'])
                ->name('books.copies.store');

            Route::patch('copies/{copy}/status', [LibraryBookController::class, 'updateCopyStatus'])
                ->name('copies.status');

            Route::resource('members', LibraryMemberController::class)->except('destroy');

            Route::get('circulation', [LibraryCirculationController::class, 'index'])->name('circulation.index');
            Route::post('circulation/checkout', [LibraryCirculationController::class, 'checkout'])->name('circulation.checkout');
            Route::post('loans/{loan}/return', [LibraryCirculationController::class, 'checkin'])->name('loans.return');
            Route::post('loans/{loan}/renew', [LibraryCirculationController::class, 'renew'])->name('loans.renew');

            Route::get('holds', [LibraryHoldController::class, 'index'])->name('holds.index');
            Route::post('books/{book}/hold', [LibraryHoldController::class, 'store'])->name('holds.store');
            Route::post('holds/{hold}/fulfill', [LibraryHoldController::class, 'fulfill'])->name('holds.fulfill');
            Route::delete('holds/{hold}', [LibraryHoldController::class, 'cancel'])->name('holds.cancel');

            Route::get('fines', [LibraryFineController::class, 'index'])->name('fines.index');
            Route::post('fines/{fine}/pay', [LibraryFineController::class, 'pay'])->name('fines.pay');
            Route::post('fines/{fine}/waive', [LibraryFineController::class, 'waive'])->name('fines.waive');

            Route::get('policy', [LibraryCirculationPolicyController::class, 'edit'])->name('policy.edit');
            Route::put('policy', [LibraryCirculationPolicyController::class, 'update'])->name('policy.update');

            // Digital Library — the Digital Librarian's collection
            // (ebooks, journal articles, papers). Unlike the Ebook/
            // Repository modules, this stays behind module.access
            // rather than a public portal, since access rights here
            // are explicitly tiered by user type.
            Route::resource('digital-resources', LibraryDigitalResourceController::class)
                ->except('destroy')
                ->parameters(['digital-resources' => 'resource']);

            Route::post('digital-resources/{resource}/publish', [LibraryDigitalResourceController::class, 'publish'])
                ->name('digital-resources.publish');

            Route::post('digital-resources/{resource}/submit-for-review', [LibraryDigitalResourceController::class, 'submitForReview'])
                ->name('digital-resources.submit-for-review');

            Route::post('digital-resources/{resource}/archive', [LibraryDigitalResourceController::class, 'archive'])
                ->name('digital-resources.archive');

            Route::get('digital-resources/{resource}/download', [LibraryDigitalResourceController::class, 'download'])
                ->name('digital-resources.download');
        });

    /*
    |--------------------------------------------------------------------------
    | RESEARCHERS' NETWORK — profiles, connections, groups, messaging,
    | and announcements
    |--------------------------------------------------------------------------
    |
    | Every Researcher/Member can build a profile, search the member
    | directory, connect with peers, join/create groups, post in
    | group discussions, and message other members directly. A Group
    | Moderator additionally approves group membership and moderates
    | discussions (gated by the 'manage-network-groups' permission);
    | an Event/Content Manager publishes calls for papers, conferences,
    | and news (gated by 'publish-announcements'). The Platform
    | Administrator manages accounts and roles via the module's own
    | admin area, already wired up above.
    |
    */

    Route::prefix('researcher')
        ->as('researcher.')
        ->middleware('module.access:researcher')
        ->group(function () {

            // Member directory & profiles
            Route::get('members', [ResearcherProfileController::class, 'index'])->name('members.index');
            Route::get('profile', [ResearcherProfileController::class, 'edit'])->name('profile.edit');
            Route::put('profile', [ResearcherProfileController::class, 'update'])->name('profile.update');
            Route::get('members/{user}', [ResearcherProfileController::class, 'show'])->name('members.show');

            // Connections
            Route::get('connections', [ResearcherConnectionController::class, 'index'])->name('connections.index');
            Route::post('connections/{user}', [ResearcherConnectionController::class, 'store'])->name('connections.store');
            Route::post('connections/{connection}/accept', [ResearcherConnectionController::class, 'accept'])->name('connections.accept');
            Route::post('connections/{connection}/decline', [ResearcherConnectionController::class, 'decline'])->name('connections.decline');
            Route::delete('connections/{connection}', [ResearcherConnectionController::class, 'destroy'])->name('connections.destroy');

            // Groups
            Route::resource('groups', ResearcherGroupController::class)->except('destroy');
            Route::post('groups/{group}/join', [ResearcherGroupController::class, 'join'])->name('groups.join');
            Route::post('groups/{group}/leave', [ResearcherGroupController::class, 'leave'])->name('groups.leave');
            Route::post('groups/{group}/members/{user}/approve', [ResearcherGroupController::class, 'approveMember'])->name('groups.members.approve');
            Route::delete('groups/{group}/members/{user}', [ResearcherGroupController::class, 'removeMember'])->name('groups.members.remove');

            // Group discussions (forum)
            Route::post('groups/{group}/posts', [ResearcherGroupPostController::class, 'store'])->name('groups.posts.store');
            Route::post('groups/{group}/posts/{post}/comments', [ResearcherGroupPostController::class, 'comment'])->name('groups.posts.comments.store');
            Route::post('groups/{group}/posts/{post}/pin', [ResearcherGroupPostController::class, 'pin'])->name('groups.posts.pin');
            Route::post('groups/{group}/posts/{post}/lock', [ResearcherGroupPostController::class, 'lock'])->name('groups.posts.lock');
            Route::delete('groups/{group}/posts/{post}', [ResearcherGroupPostController::class, 'destroy'])->name('groups.posts.destroy');

            // Direct messaging
            Route::get('messages', [ResearcherMessageController::class, 'index'])->name('messages.index');
            Route::get('messages/{user}', [ResearcherMessageController::class, 'show'])->name('messages.show');
            Route::post('messages/{user}', [ResearcherMessageController::class, 'store'])->name('messages.store');

            // Calls for papers, conferences, events, and news
            Route::resource('announcements', ResearcherAnnouncementController::class);
            Route::post('announcements/{announcement}/publish', [ResearcherAnnouncementController::class, 'publish'])->name('announcements.publish');
        });
});
