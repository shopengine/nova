<?php

namespace ShopEngine\Nova\Resources;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use ShopEngine\Nova\Actions\CodeMassEdit;
use ShopEngine\Nova\Fields\CodepoolLink;
use ShopEngine\Nova\Fields\CodeValidation;
use ShopEngine\Nova\Fields\ShopEngineModel;
use ShopEngine\Nova\Filter\ActiveCodes;
use ShopEngine\Nova\Models\CodeModel;

class Code extends ShopEngineResource
{
    public static $title = 'code';
    public static $search = ['code'];

    public static $defaultSort = '-updatedAt';
    public static $id = 'aggregateId';


    public static function getModel(): string
    {
        return CodeModel::class;
    }


    public static function label()
    {
        return __('shopengine.codes');
    }

    public static function singularLabel()
    {
        return __('shopengine.code');
    }

    public function fields(Request $request)
    {
        return $this->appendShopEngineFields([
            Text::make(__('shopengine.code'), 'code'),
            Badge::make('Status')->map([
                'enabled' => 'success',
                'disabled' => 'danger'
            ]),

            Number::make('Quantität', 'quantity')
                ->min(1)
                ->step(0)
                ->default(1)
                ->onlyOnForms()
                ->hideWhenUpdating()
                ->help('Werden mehrere erstellt, wird der Code-Name generiert.'),

            Select::make('Status')->options([
                'enabled' => 'Aktiv',
                'disabled' => 'Deaktiviert'
            ])
                ->onlyOnForms()
                ->withMeta(['value' => 'enabled']),

            Date::make('Erstellt am', 'createdAt')
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->sortable(true),
            Date::make('Aktualisiert am', 'updatedAt')
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->sortable(true),

            Text::make(__('shopengine.codepool'), 'codepoolName')
                ->onlyOnIndex(),

            CodepoolLink::make(__('shopengine.codepool'), 'codepoolId')
                ->onlyOnDetail(),

            Textarea::make('Notiz', 'note')
                ->alwaysShow(),

            ShopEngineModel::make(__('shopengine.conditionset'), 'conditionSetVersionId')
                ->model(ConditionSet::class)
                ->valueFieldName('versionId')
                ->labelFieldName('name')
                ->required(true)->rules('required')
                ->onlyOnForms(),

            ShopEngineModel::make(__('shopengine.codepool'), 'codepoolId')
                ->model(Codepool::class)
                ->labelFieldName('name')
                ->required(true)->rules('required')
                ->onlyOnForms(),

            Boolean::make('Versteckt', 'hidden')
                ->hideFromIndex(),

            CodeValidation::make('Validierungen', 'validation')
                ->hideFromIndex(),
        ]);
    }

    public function filters(Request $request)
    {
        return [
            new ActiveCodes()
        ];
    }

    public function actions(Request $request)
    {
        return [
            resolve(CodeMassEdit::class)
        ];
    }
}
