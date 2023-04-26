<?php

namespace ShopEngine\Nova;

use Illuminate\Support\Arr;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;
use ShopEngine\Nova\Contracts\ShopEnginePackageInterface;
use ShopEngine\Nova\Services\ConfiguredClassFactory;
use ShopEngine\Nova\Structs\Navigation\NavigationStruct;
use ShopEngine\Nova\Traits\UseNovaTranslations;


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
    private function buildNavigation(): array
    {
        $providers = $this->getShopengineProviders();

        $navigationData = array_filter(
            array_map(
                fn(string $provider) => $provider::getShopengineNavigation(),
                $providers
            )
        );

        // Merge Groups
        $mergedData = null;
        foreach ($navigationData as $struct) {
            if (!$mergedData) {
                $mergedData = $struct;
                continue;
            }
            $mergedData = $mergedData->mergeWith($struct);
        }

        if (!$mergedData) {
            return $navigationData;
        }

        return [
            new NavigationStruct($mergedData->getGroups()->toArray())
        ];
    }

    private function getShopengineProviders(): array
    {
        return Arr::where(
            array_keys(app()->getLoadedProviders()),
            fn($provider) => is_a($provider, ShopEnginePackageInterface::class, true)
        );
    }

    private function loadTranslationsAllPlugins(): void
    {
        $seProviders = $this->getShopengineProviders();

        foreach ($seProviders as $providerClass) {
            try {
                $file = call_user_func($providerClass . '::getLanguagePath');
                $this->loadTranslations($file, 'nova-shopengine', true);
            } catch (\Exception $e) {
            }
        }
    }
}
