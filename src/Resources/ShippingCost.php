<?php

namespace Brainspin\Novashopengine\Resources;

use App\Models\ShopSetting;
use Brainspin\Novashopengine\Fields\Money;
use Brainspin\Novashopengine\Fields\ShippingCostOptions;
use Brainspin\Novashopengine\Fields\ShippingCostValidations;
use Brainspin\Novashopengine\Models\ShippingCostModel;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Panel;

class ShippingCost extends ShopEngineResource
{
    public static $model = ShippingCostModel::class;
    public static $title = 'name';
    public static $search = ['name'];

    public static $shopEnginePath = 'shippingcost';
    public static $defaultSort = 'name';
    public static $id = 'aggregateId';

    public function fields(\Illuminate\Http\Request $request)
    {
        return [
            Text::make('Name', 'name')
                ->required(true)->rules('required')
                ->sortable(true),
            Select::make('Versandland', 'country')
                ->required(true)->rules('required')
                ->sortable(true)
                ->options([
                    "BE" => "Belgien",
                    "DK" => "Dänemark",
                    "DE" => "Deutschland",
                    "EE" => "Estland",
                    "FI" => "Finland",
                    "FR" => "Frankreich",
                    "IT" => "Italien",
                    "NL" => "Niederlande",
                    "NO" => "Norwegen",
                    "AT" => "Österreich",
                    "PL" => "Polen",
                    "PT" => "Portugal",
                    "CH" => "Schweiz",
                    "ES" => "Spanien",
                    "LU" => "Luxemburg"
                ]),

            Money::make('Preis', 'price')
                ->currency(\Shop::currency())
                ->required(true)->rules('required'),

            Number::make('Sortierungs Gewicht', 'sortWeight')
                ->min(0)
                ->max(59999)
                ->step(1)
                ->hideFromIndex(),

            Badge::make('Status')->map([
                'enabled' => 'success',
                'disabled' => 'danger',
            ]),

            Select::make('Status')->options([
                'enabled' => 'Aktiv',
                'disabled' => 'Deaktiviert'
            ])->onlyOnForms()->required(true)->rules('required'),

            Number::make('Mind. Versandzeit', 'time')
                ->required(true)->rules('required')
                ->min(1)
                ->step(1)
                ->hideFromIndex(),
            Number::make('Max. Versandzeit', 'timeMax')
                ->required(true)->rules('required')
                ->min(1)
                ->step(1)
                ->hideFromIndex(),
            Text::make('Versandschluss', 'orderDeadline')
                ->required(true)->rules('required')
                ->help('Im Format: HH:mm:ss. Bsp: 18:30:00')
                ->hideFromIndex(),
            Text::make('Warenwirtschaft Kennung', 'originIdentification')
                ->required(true)->rules('required')
                ->help('Wird zum Mapping mit der Wawi genutzt. ')
                ->hideFromIndex(),

            new Panel('Optionen', [
                ShippingCostOptions::make('Optionen', 'options')
                    ->hideFromIndex()
                    ->hideWhenCreating()
                    ->hideWhenUpdating()
                ->withMeta(['listable' => true]),
            ]),

            new Panel('Validierungen', [
                ShippingCostValidations::make('Validierungen', 'validation')
                    ->hideFromIndex()
                    ->hideWhenCreating()
                    ->hideWhenUpdating(),
            ]),
        ];
    }


}
