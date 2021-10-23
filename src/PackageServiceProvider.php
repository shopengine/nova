<?php

namespace ShopEngine\Nova;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
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

        $this->registerBindings();

        // default fields
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
    private function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/shopengine-nova.php' => config_path('shopengine-nova.php'),
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
        $configPath = __DIR__ . '/../config/shopengine-nova.php';
        $this->mergeConfigFrom($configPath, 'shopengine-nova');
    }

    private function registerBindings()
    {
        if ($this->isSeResourceRequest()) {
            app()->bind(ResourceIndexRequest::class, SeResourceIndexRequest::class);
        }
    }

    public static function getShopengineNavigation(): ?NavigationStruct
    {
        $baseNavigation = [
            new NavigationItemStruct(
                'purchases',
                '/novashopengine/purchases',
                Resources\Purchase::class,
                0
            ),
            new NavigationItemStruct(
                'shippingcosts',
                '/novashopengine/shipping-costs',
                Resources\ShippingCost::class,
                1
            ),
            new NavigationItemStruct(
                'paymentmethods',
                '/novashopengine/payment-methods',
                Resources\PaymentMethod::class,
                2
            )
        ];

        $codeNavigation = [
            new NavigationItemStruct(
                'codes',
                '/novashopengine/codes',
                Resources\Code::class,
                0
            ),
            new NavigationItemStruct(
                'codepools',
                '/novashopengine/codepools',
                Resources\Codepool::class,
                1
            )
        ];

        $struct = new NavigationStruct(
            [
                new NavigationGroupStruct(
                    'base',
                    $baseNavigation,
                    false
                ),
                new NavigationGroupStruct(
                    'codes',
                    $codeNavigation
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
