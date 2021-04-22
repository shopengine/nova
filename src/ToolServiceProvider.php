<?php

namespace Brainspin\Novashopengine;

use Brainspin\Novashopengine\Http\Middleware\Authorize;
use Brainspin\Novashopengine\Resources;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Nova;

class ToolServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerPublishing();

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'novashopengine');

        $this->app->booted(function () {
            $this->routes();
        });

        Nova::resources([
            Resources\ShippingCost::class,
            Resources\Purchase::class,
            Resources\Codepool::class,
            Resources\Code::class,
            Resources\ConditionSet::class,
            Resources\PaymentMethod::class,
            Resources\CodepoolGroup::class
        ]);

        Field::macro('default', function ($default) {
            return $this->resolveUsing(function ($value) use ($default) {
                return $value ?: $default;
            });
        });
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    private function registerPublishing() : void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/nova-shopengine.php' => config_path('shopengine-nova.php'),
            ], 'shopengine-nova-config');
            return;
        }

        $configPath = __DIR__ . '/../config/nova-shopengine.php';
        $this->mergeConfigFrom($configPath, 'nova-shopengine');
    }

    protected function routes() : void
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware(['nova', Authorize::class])
            ->prefix('nova-vendor/novashopengine')
            ->namespace('Brainspin\Novashopengine\Http\Controllers')
            ->group(__DIR__ . '/../routes/api.php');
    }

    public function register()
    {
    }
}
