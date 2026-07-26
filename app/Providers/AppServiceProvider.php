<?php

namespace App\Providers;

use App\Services\SidebarService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {

            if (auth()->check()) {

                $menus = SidebarService::getMenu();

                $view->with('sidebarMenus', $menus);

                $user = auth()->user();

                $view->with('headerNotifications', $user->notifications()->latest()->take(5)->get());
                $view->with('headerUnreadCount', $user->unreadNotifications()->count());
            }
        });
        Paginator::useBootstrapFive();
    }
}
