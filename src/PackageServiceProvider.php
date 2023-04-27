<?php

namespace ShopEngine\Nova;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Http\Requests\ResourceIndexRequest;
use Laravel\Nova\Nova;
use ShopEngine\Nova\Contracts\ShopEnginePackageInterface;
use ShopEngine\Nova\Http\Middleware\Authorize;
use ShopEngine\Nova\Http\Requests\SeResourceIndexRequest;
use ShopEngine\Nova\Resources;
use ShopEngine\Nova\Structs\Navigation\NavigationGroupStruct;
use ShopEngine\Nova\Structs\Navigation\NavigationItemStruct;
use ShopEngine\Nova\Structs\Navigation\NavigationStruct;

class PackageServiceProvider extends ServiceProvider implements ShopEnginePackageInterface
{
    protected static function ShopEngineResources()
    {
        return [
            Resources\ShippingCost::class,
            Resources\Purchase::class,
            Resources\ConditionSet::class,
            Resources\PaymentMethod::class,
            Resources\Codepool::class,
            Resources\Code::class,
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
        if ($this->isSeResourceRequest()) {
            app()->bind(ResourceIndexRequest::class, SeResourceIndexRequest::class);
        }

        // default fields
        Field::macro('default', function ($default) {
            return $this->resolveUsing(function ($value) use ($default) {
                return $value ?: $default;
            });
        });


        Nova::serving(function (ServingNova $event) {
            $files = app('files');
            if ($files->exists(__DIR__ . '/../dist/hot')) {
                Nova::remoteScript('http://localhost:8080/js/tool.js');
            } else {
                Nova::script('shopengine', __DIR__ . '/../dist/js/tool.js');
            }
        });
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    private function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/nova-shopengine.php' => config_path('shopengine-nova.php'),
            ], 'shopengine-nova-config');
            return;
        }
    }

    protected function routes(): void
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware(['nova', Authorize::class])
            ->prefix('nova-vendor/novashopengine')
            ->namespace('ShopEngine\Nova\Http\Controllers')
            ->group(__DIR__ . '/../routes/api.php');

        Route::middleware(['nova', Authorize::class])
            ->prefix('nova-api')
            ->namespace('ShopEngine\Nova\Http\Controllers')
            ->group(__DIR__ . '/../routes/web.php');
    }

    public function register()
    {
        $configPath = __DIR__ . '/../config/nova-shopengine.php';
        $this->mergeConfigFrom($configPath, 'nova-shopengine');
    }

    public static function getShopengineNavigation(): ?NavigationStruct
    {
        $baseNavigation = [
            new NavigationItemStruct('orders', '/novashopengine/purchases', Resources\Purchase::class)
        ];

        $adminNavigation = [
            new NavigationItemStruct('shippingcosts', '/novashopengine/shipping-costs', Resources\ShippingCost::class),
            new NavigationItemStruct('payments', '/novashopengine/payment-methods', Resources\PaymentMethod::class),
            new NavigationItemStruct('period-tags', '/novashopengine/shop/marketing-provider/klicktipp/period-tags', Resources\ShippingCost::class),
        ];

        $codepoolNavigation = [
            new NavigationItemStruct('codepools', '/novashopengine/codepools', Resources\Codepool::class),
            new NavigationItemStruct('codes', '/novashopengine/codes', Resources\Code::class),
        ];

        $struct = new NavigationStruct(
            [
                new NavigationGroupStruct(
                    '_',
                    $baseNavigation,
                    false
                ),
                new NavigationGroupStruct(
                    'codesgroup',
                    $codepoolNavigation
                ),
                new NavigationGroupStruct(
                    'admin',
                    $adminNavigation
                )
            ]
        );

        return $struct->getAvailableStruct(Nova::availableResources(request()));
    }

    private function isSeResourceRequest(): bool
    {
        $request = NovaRequest::createFrom(request());
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

    public static function getLanguagePath(): ?string
    {
        return __DIR__ . '/../resources/lang';
    }
}
