<?php

namespace Brainspin\Novashopengine;

use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class Novashopengine extends Tool
{
    public function boot()
    {

        dd(
            \Shop::settings()->getShopEngineShopIdentifier()
        );
        Nova::provideToScript([
            "shopEngineIdentifier" => \Shop::settings()->getShopEngineShopIdentifier(),
            "shop" => \Shop::current()
        ]);
        Nova::script('novashopengine', __DIR__.'/../dist/js/tool.js');
    }

    public function renderNavigation()
    {
        return view('novashopengine::navigation');
    }
}
