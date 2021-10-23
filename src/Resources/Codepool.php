<?php

namespace ShopEngine\Nova\Resources;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use ShopEngine\Nova\Actions\CodepoolCodeMassAssign;
use ShopEngine\Nova\Filter\CodepoolArchive;
use ShopEngine\Nova\Models\CodepoolModel;

class Codepool extends ShopEngineResource
{
    public static $title = 'orderId';
    public static $search = ['name'];

    public static $defaultSort = '-updatedAt';
    public static $id = 'id';

    public static function getModel(): string
    {
        return CodepoolModel::class;
    }

    public static function label()
    {
        return __('shopengine.codepools');
    }

    public static function singularLabel()
    {
        return __('shopengine.codepool');
    }

    public function fields(Request $request)
    {
        return $this->appendShopEngineFields([
            Text::make('Name', 'name')
                ->required(true)->rules('required')
                ->sortable(true),
            Textarea::make('Beschreibung', 'description')
                ->alwaysShow(),
            Text::make('Beschreibung', 'description')
                ->onlyOnIndex(),
            DateTime::make('Bearbeitet am', 'updatedAt')
                ->sortable(true)
                ->format('Y-MM-DD HH:mm:ss')
                ->onlyOnIndex(),
            Badge::make('Archiviert', function () {
                return $this->model->getDeletedAt() !== null;
            })
                ->map([
                   false => 'success',
                   true => 'danger'
                ])
                ->onlyOnDetail(),

            HasMany::make('Codes','codes')
        ]);
    }

    public function filters(Request $request)
    {
        return [
            new CodepoolArchive()
        ];
    }

    public function actions(Request $request)
    {
        return [
            (new CodepoolCodeMassAssign())->onlyOnDetail()
        ];
    }
}
