<?php

namespace Marshmallow\NovaActivity;

use Laravel\Nova\Nova;
use Laravel\Nova\Events\ServingNova;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Outl1ne\NovaTranslationsLoader\LoadsNovaTranslations;

class FieldServiceProvider extends ServiceProvider
{
    use LoadsNovaTranslations;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Route::middleware(['nova'])
            ->prefix('nova-vendor/nova-activity')
            ->group(__DIR__ . '/routes.php');

        Nova::serving(function (ServingNova $event) {
            Nova::script('nova-activity', __DIR__ . '/../dist/js/field.js');
            Nova::style('nova-activity', __DIR__ . '/../dist/css/field.css');
        });

        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations'),
        ], 'nova-activity-migrations');

        $this->publishes([
            __DIR__ . '/../config' => base_path('config'),
        ], 'nova-activity-config');

        $this->loadTranslations(__DIR__ . '/../resources/lang', 'nova-activity', true);

        $this->publishes([
            __DIR__ . '/../resources/lang' => $this->app->langPath('vendor/nova-activity'),
        ], 'nova-activiy-translations');
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/nova-activity.php',
            'nova-activity'
        );
    }
}
