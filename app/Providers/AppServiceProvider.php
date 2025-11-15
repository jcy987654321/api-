<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Blade;
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
        Blade::component('ad-slot', \App\View\Components\AdSlot::class);
        Blade::component('friend-links-sidebar', \App\View\Components\FriendLinksSidebar::class);
        
        View::composer('*', function ($view) {
            $view->with('siteName', SiteSetting::get('site_name', config('app.name')));
            $view->with('siteLogo', SiteSetting::get('logo'));
            $view->with('siteFavicon', SiteSetting::get('favicon'));
        });
    }
}
