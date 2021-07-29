<?php namespace ShopEngine\Nova\Resources;

use ShopEngine\Nova\Models\PurchaseModel;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;

class ConditionSet extends ShopEngineResource
{
    public static $title = 'name';
    public static $search = ['name'];

    public static $defaultSort = 'name';
    public static $id = 'aggregateId';

    public static function getModel() : string
    {
        return PurchaseModel::class;
    }

    public static function getShopEngineEndpoint(): string
    {
        return 'conditionset';
    }

    public function fields(Request $request)
    {
        return [
            Text::make('Name', 'name'),
            Number::make('Version ID', 'versionId')
        ];
    }
}
