<?php

namespace App\Providers;

use App\Services\SidebarService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
            }
        });
    }
}