<?php

namespace App\Services;

use App\Models\Module;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class SidebarService
{
    /**
     * Build the sidebar for the current user.
     *
     * Every item has: title, icon, route (a named route).
     * An item may optionally have a 'children' array of the same shape,
     * used here for a module's own admin link.
     */
    public static function getMenu(): array
    {
        $user = Auth::user();

        if (! $user) {
            return [];
        }

        $menu = [];

        $menu[] = [
            'title' => 'Dashboard',
            'icon' => 'bi-grid',
            'route' => 'dashboard',
        ];

        if ($user->isSuperAdmin()) {

            $menu[] = [
                'title' => 'Administration',
                'icon' => 'bi-speedometer2',
                'route' => 'admin.dashboard',
                'children' => [
                    ['title' => 'Users', 'icon' => 'bi-people', 'route' => 'admin.users.index'],
                    ['title' => 'Modules', 'icon' => 'bi-puzzle', 'route' => 'admin.modules.index'],
                    ['title' => 'Roles', 'icon' => 'bi-person-badge', 'route' => 'admin.roles.index'],
                    ['title' => 'Permissions', 'icon' => 'bi-key', 'route' => 'admin.permissions.index'],
                ],
            ];

            $modules = Module::active()->orderBy('name')->get();

            // One place for every platform-wide toggle plus every
            // module's own payment configuration — instead of each
            // being reachable only by typing the URL directly.
            // 'General' always shows; a module's payment settings
            // link only shows up once that module actually defines
            // a {code}.settings.edit route, so adding a settings
            // page to a new module is all it takes to appear here.
            $settingsChildren = [
                ['title' => 'General', 'icon' => 'bi-toggles', 'route' => 'admin.settings.edit'],
            ];

            foreach ($modules as $module) {
                $moduleSettingsRoute = "{$module->code}.settings.edit";

                if (Route::has($moduleSettingsRoute)) {
                    $settingsChildren[] = [
                        'title' => "{$module->name} Payment",
                        'icon' => 'bi-cash-coin',
                        'route' => $moduleSettingsRoute,
                    ];
                }
            }

            $menu[] = [
                'title' => 'Settings',
                'icon' => 'bi-gear',
                'route' => 'admin.settings.edit',
                'children' => $settingsChildren,
            ];

        } else {

            $moduleIds = $user->moduleRoles()->pluck('roles.module_id')->unique();

            $modules = Module::whereIn('id', $moduleIds)->orderBy('name')->get();
        }

        foreach ($modules as $module) {

            $dashboardRoute = "{$module->code}.dashboard";

            if (! Route::has($dashboardRoute)) {
                continue;
            }

            $item = [
                'title' => $module->name,
                'icon' => $module->icon ?: 'bi-circle',
                'route' => $dashboardRoute,
            ];

            $children = [];

            // Journal Management's own feature: visible to any member,
            // not just its admin.
            if ($module->code === 'journal' && Route::has('journal.manuscripts.index')) {
                $children[] = ['title' => 'Manuscripts', 'icon' => 'bi-file-earmark-text', 'route' => 'journal.manuscripts.index'];

                if ($user->hasModulePermission('journal', 'manage-categories') && Route::has('journal.categories.index')) {
                    $children[] = ['title' => 'Categories', 'icon' => 'bi-tags', 'route' => 'journal.categories.index'];
                }
            }

            // Ebook Publishing's own feature: visible to any member,
            // not just its admin.
            if ($module->code === 'ebook' && Route::has('ebook.books.index')) {
                $children[] = ['title' => 'Books', 'icon' => 'bi-book', 'route' => 'ebook.books.index'];
                $children[] = ['title' => 'Digital Library', 'icon' => 'bi-globe', 'route' => 'ebook.public.index'];

                if ($user->hasModulePermission('ebook', 'manage-categories') && Route::has('ebook.categories.index')) {
                    $children[] = ['title' => 'Categories', 'icon' => 'bi-tags', 'route' => 'ebook.categories.index'];
                }
            }

            // Repository Management's own feature: visible to any
            // member, not just its admin.
            if ($module->code === 'repository' && Route::has('repository.items.index')) {
                $children[] = ['title' => 'Items', 'icon' => 'bi-file-earmark-text', 'route' => 'repository.items.index'];
                $children[] = ['title' => 'Public Repository', 'icon' => 'bi-globe', 'route' => 'repository.public.index'];
            }

            // Library Management's own features: visible to any
            // member, not just its admin. 'Members' and 'Circulation
            // Policy' are only shown to users who actually hold the
            // permission those pages require, so no one gets a link
            // that just 403s.
            if ($module->code === 'library' && Route::has('library.books.index')) {
                $children[] = ['title' => 'Catalog', 'icon' => 'bi-book', 'route' => 'library.books.index'];

                if ($user->hasModulePermission('library', 'catalog-items')) {
                    $children[] = ['title' => 'Add New Title', 'icon' => 'bi-journal-plus', 'route' => 'library.books.create'];
                }

                if (
                    $user->hasModulePermission('library', 'catalog-items')
                    || $user->hasModulePermission('library', 'approve-acquisitions')
                    || $user->hasModulePermission('library', 'manage-acquisitions')
                ) {
                    $children[] = [
                        'title' => 'Pending Acquisitions',
                        'icon' => 'bi-hourglass-split',
                        'route' => 'library.books.index',
                        'params' => ['status' => 'pending_acquisition'],
                    ];
                }

                if ($user->hasModulePermission('library', 'manage-inventory') && Route::has('library.copies.index')) {
                    $children[] = ['title' => 'Stocktake / Copies', 'icon' => 'bi-clipboard-check', 'route' => 'library.copies.index'];
                }

                $children[] = ['title' => 'Digital Library', 'icon' => 'bi-cloud-arrow-down', 'route' => 'library.digital-resources.index'];

                if ($user->hasModulePermission('library', 'manage-digital-collection') || $user->hasModulePermission('library', 'submit-digital-content')) {
                    $children[] = ['title' => 'Upload Resource', 'icon' => 'bi-cloud-upload', 'route' => 'library.digital-resources.create'];
                }

                $isCirculationStaff = $user->isSuperAdmin() || $user->hasModulePermission('library', 'manage-circulation');

                $children[] = [
                    'title' => $isCirculationStaff ? 'Circulation Desk' : 'My Loans',
                    'icon' => 'bi-arrow-left-right',
                    'route' => 'library.circulation.index',
                ];

                if ($user->hasModulePermission('library', 'manage-circulation')) {
                    $children[] = ['title' => 'Members', 'icon' => 'bi-people', 'route' => 'library.members.index'];
                }

                $children[] = ['title' => 'Holds', 'icon' => 'bi-bookmark', 'route' => 'library.holds.index'];
                $children[] = ['title' => 'Fines', 'icon' => 'bi-cash-coin', 'route' => 'library.fines.index'];

                if ($user->hasModulePermission('library', 'manage-circulation-policy')) {
                    $children[] = ['title' => 'Circulation Policy', 'icon' => 'bi-sliders', 'route' => 'library.policy.edit'];
                }

                if ($user->hasModulePermission('library', 'manage-categories') && Route::has('library.categories.index')) {
                    $children[] = ['title' => 'Categories', 'icon' => 'bi-tags', 'route' => 'library.categories.index'];
                }

                if ($user->hasModulePermission('library', 'manage-settings') && Route::has('library.pricing-plans.index')) {
                    $children[] = ['title' => 'Pricing Plans', 'icon' => 'bi-cash-coin', 'route' => 'library.pricing-plans.index'];
                }
            }

            // Researchers' Network's own features: visible to any
            // member, not just its admin.
            if ($module->code === 'researcher' && Route::has('researcher.members.index')) {
                $children[] = ['title' => 'My Profile', 'icon' => 'bi-person-badge', 'route' => 'researcher.profile.edit'];
                $children[] = ['title' => 'Members', 'icon' => 'bi-people', 'route' => 'researcher.members.index'];
                $children[] = ['title' => 'Connections', 'icon' => 'bi-diagram-3', 'route' => 'researcher.connections.index'];
                $children[] = ['title' => 'Groups', 'icon' => 'bi-collection', 'route' => 'researcher.groups.index'];
                $children[] = ['title' => 'Messages', 'icon' => 'bi-chat-dots', 'route' => 'researcher.messages.index'];
                $children[] = ['title' => 'Announcements', 'icon' => 'bi-megaphone', 'route' => 'researcher.announcements.index'];
            }

            $adminRoute = "{$module->code}.admin.dashboard";

            if (Route::has($adminRoute) && $user->isModuleAdmin($module->code)) {

                $children[] = ['title' => "{$module->name} Admin", 'icon' => 'bi-gear', 'route' => $adminRoute];

                $usersRoute = "{$module->code}.admin.users.index";

                if (Route::has($usersRoute)) {
                    $children[] = ['title' => 'Manage Users', 'icon' => 'bi-people', 'route' => $usersRoute];
                }
            }

            if (! empty($children)) {
                $item['children'] = $children;
            }

            $menu[] = $item;
        }

        return $menu;
    }
}