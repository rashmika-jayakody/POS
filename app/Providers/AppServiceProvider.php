<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->extend('translation.loader', function ($currentLoader) {
            return new \App\Translation\MergeTranslationLoader(
                $this->app['files'],
                $this->app->langPath()
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $settings = auth()->user()?->tenant?->businessSetting;
            $view->with([
                'currencySymbol' => $settings?->currency_symbol ?? 'Rs',
                'currencyCode' => $settings?->currency_code ?? 'LKR',
            ]);
        });
    }
}
