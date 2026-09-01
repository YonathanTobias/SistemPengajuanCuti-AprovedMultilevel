<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
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
        // Share isLemburEnabled to all views
        View::composer('*', function ($view) {
            $isLembur = false;
            try {
                if (Schema::hasTable('settings')) {
                    $isLembur = Setting::isLemburEnabled();
                }
            } catch (\Throwable $e) {
                $isLembur = false;
            }
            $view->with('isLemburEnabled', $isLembur);
        });
    }
}
