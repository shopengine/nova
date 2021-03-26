<?php
namespace Brainspin\Novashopengine\Services;

use Brainspin\Novashopengine\Contracts\NovaShopEngineInterface;

class ConfiguredClassFactory {

    public static function getShopEngineService() : NovaShopEngineInterface
    {
        return app()->make(\Config::get('nova-shopengine.nova_shopengine_interface'));
    }

    // this one needs a real class
    public static function createCodepoolClass()
    {
        $className = \Config::get('nova-shopengine.codepool_model');
        return new $className;
    }

}
