<?php

namespace Brainspin\Novashopengine;

use Brainspin\Novashopengine\Contracts\ShopEnginePackageInterface;
use Brainspin\Novashopengine\Http\Requests\SeResourceIndexRequest;
use Brainspin\Novashopengine\Services\ConfiguredClassFactory;
use Brainspin\Novashopengine\Structs\Navigation\NavigationStruct;
use Brainspin\Novashopengine\Traits\UseNovaTranslations;
use Illuminate\Support\Arr;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Http\Requests\ResourceIndexRequest;
use Laravel\Nova\Nova;
use Laravel\Nova\ResourceCollection;
use Laravel\Nova\Tool;


class Novashopengine extends Tool
{
    use UseNovaTranslations;

    public function boot()
    {
        $this->renderNavigation();

        $this->loadTranslations(__DIR__ . '/../resources/lang', 'nova-shopengine', true);

        $shopService = ConfiguredClassFactory::getShopEngineService();

        Nova::provideToScript([
            "shopEngineIdentifier" => $shopService->shopEngineSettings()->getShopEngineShopIdentifier(),
            "shopCurrency" => $shopService->shopCurrency()
        ]);

        Nova::script('novashopengine', __DIR__.'/../dist/js/tool.js');
    }

    public function renderNavigation()
    {
        return view('novashopengine::navigation.navigation', [
            'structs' => $this->buildNavigation()
        ]);
    }

    /**
     * @return NavigationStruct[]
     */
    private function buildNavigation() : array
    {
        $providers = $this->getShopengineProviders();

        $navigationData = array_filter(
            array_map(
                fn(string $provider) => $provider::getShopengineNavigation(),
                $providers
            )
        );

        return $navigationData;
    }

    private function getShopengineProviders() : array {
        return Arr::where(
            array_keys(app()->getLoadedProviders()),
            fn($provider) => is_a($provider, ShopEnginePackageInterface::class, true)
        );
    }
}
