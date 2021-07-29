<?php
namespace ShopEngine\Nova\Services;

use ShopEngine\Nova\Contracts\NovaShopEngineInterface;

class ConfiguredClassFactory {

    public static function getShopEngineService() : NovaShopEngineInterface
    {
        $serviceClass = \Config::get('nova-shopengine.nova_shopengine_interface');

        if (is_null($serviceClass)) {
            throw new \Exception(
                    'Nova ShopEngine Service has not been defined. You need to define a nova-shopengine.nova_shopengine_interface service in the config. It must be a service using the NovaShopengineInterface.'
                );
        }

        return app()->make($serviceClass);
    }

}
