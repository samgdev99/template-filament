<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->isLocal()) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);

            if (class_exists(\App\Providers\TelescopeServiceProvider::class)) {
                $this->app->register(\App\Providers\TelescopeServiceProvider::class);
            }
        }
    }

    public function boot(): void
    {
        //
    }
}
