<?php

namespace ShopEngine\Nova\Resources;

use Laravel\Nova\Fields\Date;
use ShopEngine\Nova\Models\ConditionSetModel;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Code as CodeField;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;

class ConditionSet extends ShopEngineResource
{
    public static $title = 'name';
    public static $search = ['name'];

    public static $defaultSort = '-updatedAt';
    public static $id = 'aggregateId';

    public static function getModel(): string
    {
        return ConditionSetModel::class;
    }

    public function authorizedToDelete(Request $request)
    {
        return false;
    }

    public function authorizedToUpdate(Request $request)
    {
        return false;
    }

    public static function label()
    {
        return __('shopengine.conditionsets');
    }

    public static function singularLabel()
    {
        return __('shopengine.conditionset');
    }

    public function fields(Request $request)
    {
        return $this->appendShopEngineFields([
            Text::make('Name', 'name')
                ->rules('required')
                ->required()
                ->sortable(true),
            Number::make('Version Id', 'versionId')
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->readonly(),
            Number::make('Gruppe', 'group')
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->readonly(),
            Date::make('Aktualisiert am', 'updatedAt')
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->readonly()
                ->sortable(true),
            CodeField::make('Regeln', 'conditions')
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->readonly()
                ->json()
                ->autoHeight()
        ]);
    }
}
