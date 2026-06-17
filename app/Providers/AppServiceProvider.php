<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

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
        // Set PHP's default timezone to Asia/Manila
        date_default_timezone_set('Asia/Manila');

        // Optional: Set Carbon locale if you use translated dates
        Carbon::setLocale(Config::get('app.locale'));
    }
}
