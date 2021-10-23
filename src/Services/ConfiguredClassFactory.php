<?php

namespace ShopEngine\Nova\Services;

use ShopEngine\Nova\Contracts\ShopEngineNovaInterface;

class ConfiguredClassFactory
{
    public static function getShopEngineService(): ShopEngineNovaInterface
    {
        $serviceClass = \Config::get('shopengine-nova.shopengine_nova_interface');

        if (is_null($serviceClass)) {
            throw new \Exception(
                'Nova ShopEngine Service has not been defined. You need to define a shopengine-nova.shopengine_nova_interface service in the config. It must be a service using the ShopEngineNovaInterface.'
            );
        }

        return app()->make($serviceClass);
    }
}
