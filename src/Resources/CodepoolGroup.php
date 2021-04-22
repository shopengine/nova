<?php

namespace Brainspin\Novashopengine\Resources;

use App\Nova\ShopResource;
use Brainspin\Novashopengine\Fields\CodepoolActions;
use Brainspin\Novashopengine\Fields\CodepoolGroupCodepools;
use Brainspin\Novashopengine\Fields\CodepoolStatistics;
use Brainspin\Novashopengine\Services\ConfiguredClassFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Panel;
use OptimistDigital\MultiselectField\Multiselect;
use SSB\Api\Model\Codepool as CodepoolModel;

// Outsource Shopresource
class CodepoolGroup extends ShopResource
{
    public static $canUseFallbackShop = false;

    public static $globallySearchable = false;
    public static $displayInNavigation = false;
    public static $canImportResource = false;

    // Make Model dynamic to Configs
    public static function newModel() {
        $model = ConfiguredClassFactory::createCodepoolClass();
        return new $model;
    }

    public static $search = [
        'title',
    ];

    public function fields(Request $request)
    {
        $shopService = ConfiguredClassFactory::getShopEngineService();
        $api = $shopService->shopEngineClient(
            $shopService->shopEngineSettings()
        );

        $isFormAction =
            Str::after($request->route()->getAction()['controller'], '@') === 'fields' ||
            $request->route()->getAction()['controller'] === 'Laravel\Nova\Http\Controllers\CreationFieldController@index';

        return [
            Text::make('Name', 'title')
                ->required(true)->rules('required')
                ->sortable(true),
            Textarea::make('Beschreibung', 'description')
                ->alwaysShow(),

            Multiselect::make('Marketing Kampagnen', 'codepools')
                ->options(function() use ($api, $isFormAction) {
                    if (!$isFormAction) {
                        return [];
                    }

                    return collect($api->get('codepool', ['properties' => 'id|name']))
                        ->keyBy(fn(CodepoolModel $codepool) => $codepool->getId())
                        ->map(fn(CodepoolModel $codepool) => $codepool->getName())
                        ->toArray();
                })
                ->onlyOnForms(),

            CodepoolStatistics::make('Statistiken')->onlyOnDetail(),

            new Panel('Weitere Aktionen', [
                CodepoolActions::make('Weitere Aktionen')->onlyOnDetail(),
            ]),

            new Panel('Marketing Kamapagnen', [
                CodepoolGroupCodepools::make('Marketing Kamapagnen', 'codepools')
                    ->onlyOnDetail()
            ]),
        ];
    }
}
