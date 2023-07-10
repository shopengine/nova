<?php

namespace ShopEngine\Nova\Resources;

use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
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
        return __('se.codelesses');
    }

    public static function singularLabel()
    {
        return __('se.codeless');
    }

    public function fields(Request $request)
    {
        return $this->appendShopEngineFields([
            Text::make('Name', 'name')
                ->sortable(true)
                ->hideWhenUpdating(),
            Badge::make('Status')->map([
                'enabled' => 'success',
                'disabled' => 'danger'
            ]),
            Select::make('Status')->options([
                'enabled' => 'Aktiv',
                'disabled' => 'Deaktiviert'
            ])->onlyOnForms()
                ->withMeta(['value' => 'enabled']),
            Date::make('Erstellt am', 'createdAt')
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->sortable(true),
            Date::make('Aktualisiert am', 'updatedAt')
                ->hideWhenUpdating()
                ->sortable(true),
            Textarea::make('Notiz', 'note')
                ->hideWhenUpdating()
        ]);
    }

    public function filters(Request $request)
    {
        return [];
    }

    public static function authorizedToCreate(Request $request)
    {
        return false;
    }

}

