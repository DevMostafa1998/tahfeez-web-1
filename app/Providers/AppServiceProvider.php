<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use App\Models\Center;

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
        Schema::defaultStringLength(length: 191);
        View::composer('includes.header', function ($view) {
            $view->with('header_centers', Center::all());
            $view->with('selected_center_id', session('selected_center_id'));
        });
        View::composer('*', function ($view) {
            $view->with('selected_gender', session('selected_gender'));
        });
    }
}
