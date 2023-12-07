<?php

namespace Marshmallow\Comments;

use Laravel\Nova\Nova;
use Laravel\Nova\Events\ServingNova;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class FieldServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Route::middleware(['nova'])
            ->prefix('nova-vendor/nova-comments')
            ->group(__DIR__ . '/routes.php');

        Nova::serving(function (ServingNova $event) {
            Nova::script('comments', __DIR__ . '/../dist/js/field.js');
            Nova::style('comments', __DIR__ . '/../dist/css/field.css');
        });

        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations'),
        ], 'nova-commentable-migrations');

        $this->publishes([
            __DIR__ . '/../config' => base_path('config'),
        ], 'nova-commentable-config');
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/nova-commentable.php',
            'nova-commentable'
        );
    }
}
