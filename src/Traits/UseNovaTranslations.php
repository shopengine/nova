<?php

namespace ShopEngine\Nova\Traits;

use Laravel\Nova\Nova;
use OptimistDigital\NovaTranslationsLoader\LoadsNovaTranslations;

trait UseNovaTranslations
{
    use LoadsNovaTranslations;

    /**
     * @todo interim override for blade template (NovaTranslations loads only for vue)
     *
     * @param $pckgTransDir
     * @param $pckgName
     * @param $publish
     */
    private function translations($pckgTransDir, $pckgName, $publish)
    {
        if (app()->runningInConsole() && $publish) {
            $this->publishes([$pckgTransDir => resource_path("lang/vendor/{$pckgName}")], 'translations');
            return;
        }

        if (!method_exists('Nova', 'translations')) {
            throw new Exception('Nova::translations method not found, please ensure you are using the correct version of Nova.');
        }

        $serve = function () use ($pckgTransDir, $pckgName) {
            $locale = app()->getLocale();
            $fallbackLocale = config('app.fallback_locale');

            // Load Laravel translations
            $this->loadLaravelTranslations($pckgTransDir, $pckgName);

            // Attempt to load Nova translations
            if ($this->loadNovaTranslations($locale, 'project', $pckgTransDir, $pckgName)) {
                return;
            }
            if ($this->loadNovaTranslations($locale, 'local', $pckgTransDir, $pckgName)) {
                return;
            }
            if ($this->loadNovaTranslations($fallbackLocale, 'project', $pckgTransDir, $pckgName)) {
                return;
            }
            if ($this->loadNovaTranslations($fallbackLocale, 'local', $pckgTransDir, $pckgName)) {
                return;
            }
            $this->loadNovaTranslations('en', 'local', $pckgTransDir, $pckgName);
        };

        Nova::serving($serve);
        $serve();
    }
}
