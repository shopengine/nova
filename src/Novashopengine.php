<?php

namespace Brainspin\Novashopengine;

use Brainspin\Novashopengine\Contracts\NovaShopEngineInterface;
use Brainspin\Novashopengine\Services\ConfiguredClassFactory;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class Novashopengine extends Tool
{
    public function boot()
    {
        $shopService = ConfiguredClassFactory::getShopEngineService();

        Nova::provideToScript([
            "shopEngineIdentifier" => $shopService->shopEngineSettings()->getShopEngineShopIdentifier(),
            "shopCurrency" => $shopService->shopCurrency()
        ]);
        Nova::script('novashopengine', __DIR__.'/../dist/js/tool.js');
    }

    public function renderNavigation()
    {
        return view('novashopengine::navigation');
    }
}
