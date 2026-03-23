<?php

namespace App\Providers;

use App\Services\ApiUsageLogger;
use App\Services\ApiUsageReportService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ApiUsageLogger::class);
        $this->app->singleton(ApiUsageReportService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
