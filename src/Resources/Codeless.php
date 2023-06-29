<?php

namespace ShopEngine\Nova\Resources;

use ShopEngine\Nova\Filter\ActiveCodes;
use ShopEngine\Nova\Models\CodelessModel;
use Illuminate\Http\Request;

class Codeless extends ShopEngineResource
{
    public static $title = 'name';
    public static $search = ['name'];

    public static $defaultSort = '-updatedAt';
    public static $id = 'aggregateId';


    public static function getModel() : string
    {
        return CodelessModel::class;
    }

    public static function getShopEngineEndpoint(): string
    {
        return 'codeless';
    }

    public static function label()
    {
        return __('se.codeless');
    }

    public static function singularLabel()
    {
        return __('se.codeless');
    }

    public function fields(Request $request) {
        return [];
    }

    public function filters(Request $request)
    {
        return [
            new ActiveCodes()
        ];
    }
}
