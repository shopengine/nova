<?php

namespace ShopEngine\Nova\Resources;

use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use ShopEngine\Nova\Fields\ToggleCodelessStatus;
use ShopEngine\Nova\Models\CodelessModel;
use Illuminate\Http\Request;


class Codeless extends ShopEngineResource
{
    public static $title = 'name';
    public static $search = ['name'];

    public static $defaultSort = 'createdAt';
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

    public function fields(Request $request)
    {
        return $this->appendShopEngineFields([
            Text::make('Codeless', 'name')
                ->hideWhenUpdating()
                ->sortable(true),
            Badge::make('Status')->map([
                'enabled' => 'success',
                'disabled' => 'danger'
            ]),
            Date::make('Erstellt am', 'createdAt')
                ->hideWhenCreating()
                ->hideWhenUpdating(),
            Date::make('Aktualisiert am', 'updatedAt')
                ->hideWhenUpdating()
                ->sortable(true),
            Textarea::make('Notiz', 'note')
                ->hideWhenUpdating(),
            ToggleCodelessStatus::make('', 'status')
            ->withMeta(['aggregateId' => $this->attributes('aggregateId')->data['aggregateId']])
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

    public function authorizedToUpdate(Request $request)
    {
        return false;
    }

}

