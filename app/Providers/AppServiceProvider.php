<?php

namespace App\Providers;

use App\Support\SiteContent;
use App\Support\SiteSettings;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! $this->app->runningInConsole()) {
            $path = request()->path();

            if ($path === 'admin' || str_starts_with($path, 'admin/') || $path === 'livewire' || str_starts_with($path, 'livewire/')) {
                app()->setLocale('ru');
            }
        }

        View::composer(['layouts.*', 'pages.*', 'components.*'], function ($view): void {
            $options = SiteContent::options();

            $view->with('siteOptions', $options);
            $view->with('homePage', SiteContent::home());
            $view->with('siteServices', SiteContent::services());
            $view->with('siteName', $options->displaySiteName());
            $view->with('homepageVariant', SiteSettings::homepage());
            $view->with('visualizationAnimated', SiteSettings::animated());
        });
    }
}
