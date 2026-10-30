<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;

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
        //

        Model::preventLazyLoading(! app()->isProduction());

        // Instead of throwing an exception, just log it
        Model::handleLazyLoadingViolationUsing(function (Model $model, string $relation) {
            $class = get_class($model);
            logger()->warning("Attempted to lazy load [{$relation}] on model [{$class}].");
        });
    }
}