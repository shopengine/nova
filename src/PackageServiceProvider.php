<?php

namespace Brainspin\Novashopengine;

use Brainspin\Novashopengine\Contracts\ShopEnginePackageInterface;
use Brainspin\Novashopengine\Http\Middleware\Authorize;
use Brainspin\Novashopengine\Http\Requests\SeResourceIndexRequest;
use Brainspin\Novashopengine\Resources;
use Brainspin\Novashopengine\Structs\Navigation\NavigationGroupStruct;
use Brainspin\Novashopengine\Structs\Navigation\NavigationItemStruct;
use Brainspin\Novashopengine\Structs\Navigation\NavigationStruct;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Http\Requests\ResourceIndexRequest;
use Laravel\Nova\Nova;

class PackageServiceProvider extends ServiceProvider implements ShopEnginePackageInterface
{
    protected static function ShopEngineResources() {
        return [
            Resources\ShippingCost::class,
            Resources\Purchase::class,
            Resources\ConditionSet::class,
            Resources\PaymentMethod::class
        ];
    }

    public function boot()
    {
        $this->registerPublishing();

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'novashopengine');

        $this->app->booted(function () {
            $this->routes();
        });

        Nova::resources(self::ShopEngineResources());

        // intercept nova request
        if ($this->isSeResourceRequest())
        {
            app()->bind(ResourceIndexRequest::class, SeResourceIndexRequest::class);
        }

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

        Route::middleware(['nova', Authorize::class])
            ->prefix('nova-api')
            ->namespace('Brainspin\Novashopengine\Http\Controllers')
            ->group(__DIR__ . '/../routes/web.php');
    }

    public function register()
    {
    }

    public static function getShopengineNavigation() : ?NavigationStruct {
        $baseNavigation = [
            new NavigationItemStruct('orders', '/novashopengine/purchases')
        ];

        $adminNavigation = [
            new NavigationItemStruct('shippingcosts', '/novashopengine/shipping-costs'),
            new NavigationItemStruct('payments', '/novashopengine/payment-methods')
        ];

        return new NavigationStruct(
            [
                new NavigationGroupStruct(
                    '_',
                    $baseNavigation,
                    false
                ),
                new NavigationGroupStruct(
                    'admin',
                    $adminNavigation
                ),
            ]
        );
    }

    private function isSeResourceRequest() : bool
    {
        $request = app(NovaRequest::class);
        $resource = $request->viaResource();

        if (is_null($resource) && $request->segment(1) === 'nova-api') {
            $resourceString = $request->segment(2);
            $resource = Nova::resourceForKey($resourceString);
        }

        return in_array(
            $resource,
            self::ShopEngineResources()
        );
    }
}
