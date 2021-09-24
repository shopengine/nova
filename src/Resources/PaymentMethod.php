<?php

namespace ShopEngine\Nova\Resources;

use ShopEngine\Nova\Models\PaymentMethodModel;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;

class PaymentMethod extends ShopEngineResource
{
    public static $title = 'name';
    public static $search = ['name'];

    public static $defaultSort = 'name';
    public static $id = 'aggregateId';

    public static function getModel() : string
    {
        return PaymentMethodModel::class;
    }

    public function fields(\Illuminate\Http\Request $request)
    {
        return $this->appendShopEngineFields([
            Text::make('Name', 'name')
                ->required(true)->rules('required'),
            Text::make('Type', 'type')->readonly(),

            Badge::make('Status')->map([
                'enabled' => 'success',
                'disabled' => 'danger',
            ]),

            Select::make('Status')->options([
                'enabled' => 'Aktiv',
                'disabled' => 'Deaktiviert'
            ])->onlyOnForms()->required(true)->rules('required'),

            Number::make('Gewicht', 'weight')
                ->min(1)
                ->step(1)
                ->required(true)->rules('required')
                ->sortable()
        ]);
    }

    public static function authorizedToCreate(Request $request)
    {
        return false;
    }
}
