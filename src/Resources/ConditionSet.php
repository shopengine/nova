<?php namespace Brainspin\Novashopengine\Resources;

use Brainspin\Novashopengine\Models\PurchaseModel;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;

class ConditionSet extends ShopEngineResource
{
    public static $model = PurchaseModel::class;
    public static $title = 'name';
    public static $search = ['name'];

    public static $defaultSort = 'name';
    public static $id = 'aggregateId';

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
