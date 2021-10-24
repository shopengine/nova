<?php

namespace ShopEngine\Nova;

use ShopEngine\Nova\Contracts\ShopEnginePackageInterface;
use ShopEngine\Nova\Services\ConfiguredClassFactory;
use ShopEngine\Nova\Structs\Navigation\NavigationGroupStruct;
use ShopEngine\Nova\Structs\Navigation\NavigationStruct;
use ShopEngine\Nova\Traits\UseNovaTranslations;
use Illuminate\Support\Arr;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class NovaTool extends Tool
{
    use UseNovaTranslations;

    public function boot()
    {
        $this->renderNavigation();

        $this->loadTranslationsAllPlugins();

        $shopService = ConfiguredClassFactory::getShopEngineService();

        Nova::provideToScript([
            "shopEngineIdentifier" => $shopService->shopEngineSettings()->getShopEngineShopIdentifier(),
            "shopCurrency" => $shopService->shopCurrency()
        ]);

        Nova::script('shopengine', __DIR__.'/../dist/js/tool.js');
    }

    public function renderNavigation()
    {
        return view('shopengine::navigation.navigation', [
            'structs' => $this->buildNavigation()
        ]);
    }

    /**
     * @return NavigationStruct[]
     */
    private function buildNavigation(): array
    {
        /** @var NavigationStruct[] $navigationData */
        $navigationData = array_filter(
            array_map(
                fn (string $provider) => $provider::getShopengineNavigation(),
                $this->getShopengineProviders()
            )
        );

        $mergedNavigation = null;

        /** @var NavigationStruct $navigation */
        foreach ($navigationData as $navigation) {
            if (! $mergedNavigation) {
                $mergedNavigation = $navigation;
                continue;
            }

            $mergedNavigation = $mergedNavigation->mergeWith($navigation);
        }

        if (! $mergedNavigation) {
            return $navigationData;
        }

        $mergedNavigation->sortGroups(config('shopengine-nova.navigation'));

        return [
            new NavigationStruct($mergedNavigation->getGroups()->toArray())
        ];
    }

    private function getShopengineProviders(): array
    {
        return Arr::where(
            array_keys(app()->getLoadedProviders()),
            fn ($provider) => is_a($provider, ShopEnginePackageInterface::class, true)
        );
    }

    private function loadTranslationsAllPlugins(): void
    {
        $seProviders = $this->getShopengineProviders();

        foreach ($seProviders as $providerClass) {
            try {
                $file = call_user_func($providerClass .'::getLanguagePath');
                $this->loadTranslations($file, 'shopengine-nova', true);
            } catch (\Exception $e) {
            }
        }
    }
}
