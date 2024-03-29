<?php

namespace ShopEngine\Nova\Resources;

use Carbon\Carbon;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use ShopEngine\Nova\Fields\Toggle;
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
            Date::make('Erstellt am', 'createdAt')
                ->hideWhenCreating()
                ->hideWhenUpdating(),
            Date::make('Aktualisiert am', 'updatedAt')
                ->hideWhenUpdating()
                ->sortable(true),
            Textarea::make('Notiz', 'note')
                ->hideWhenUpdating(),
            DateTime::make('Beginnt Am', 'start')
                ->hideWhenUpdating()
                ->sortable(true)
                ->displayUsing(function ($value) {
                    $value = Carbon::parse($value);
                    return $value->tz(config('app.timezone'))->format('Y-m-d H:i:s');
                }),
            DateTime::make('Endet Am', 'end')
                ->hideWhenUpdating()
                ->sortable(true)
                ->displayUsing(function ($value) {
                    return (Carbon::parse($value))->tz(config('app.timezone'))->format('Y-m-d H:i:s');
                }),
            Toggle::make('Status', 'status')
                ->withMeta([
                    'singularResourceName' => 'codeless',
                    'aggregateId' => $this->attributes('aggregateId')->data['aggregateId']
                ]),
            Badge::make('Status', 'status')
                ->map([
                    'enabled' => 'success',
                    'disabled' => 'danger'
                ])
                ->onlyOnDetail(),
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

