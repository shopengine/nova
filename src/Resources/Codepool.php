<?php

namespace ShopEngine\Nova\Resources;

use App\Nova\CodeRedirect;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use ShopEngine\Nova\Actions\CodepoolCodeMassAssign;
use ShopEngine\Nova\Filter\CodepoolArchive;
use ShopEngine\Nova\Models\CodepoolModel;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;

class Codepool extends ShopEngineResource
{
    public static $title = 'orderId';
    public static $search = ['name'];

    public static $defaultSort = '-updatedAt';
    public static $id = 'id';

    public static function getModel() : string
    {
        return CodepoolModel::class;
    }

    public static function label()
    {
        return __('se.codepools');
    }

    public static function singularLabel()
    {
        return __('se.codepool');
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
            Badge::make('Archiviert', function() {
                return $this->model->getDeletedAt() !== null;
            })
                ->map([
                   false => 'success',
                   true => 'danger'
                ])
                ->onlyOnDetail(),
            //\ShopEngine\Nova\Fields\ShopEngineModel::make('Codes','codes')
            HasMany::make('codes')
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
