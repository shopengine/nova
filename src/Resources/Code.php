<?php

namespace Brainspin\Novashopengine\Resources;

use Brainspin\Novashopengine\Fields\CodepoolLink;
use Brainspin\Novashopengine\Fields\CodeValidation;
use Brainspin\Novashopengine\Fields\ShopEngineModel;
use Brainspin\Novashopengine\Filter\ActiveCodes;
use Brainspin\Novashopengine\Models\CodeModel;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;

class Code extends ShopEngineResource
{
    public static $title = 'code';
    public static $search = ['code'];

    public static $defaultSort = '-updatedAt';
    public static $id = 'aggregateId';


    public static function getModel() : string
    {
        return CodeModel::class;
    }

    public static function getShopEngineEndpoint(): string
    {
        return 'code';
    }

    public function fields(Request $request)
    {
        return $this->appendShopEngineFields([
            Text::make('Code', 'code'),
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

            Text::make('Marketing Kampagne', 'codepoolName')
                ->onlyOnIndex(),

            CodepoolLink::make('Marketing Kampagne', 'codepoolId')
                ->onlyOnDetail(),

            Textarea::make('Notiz', 'note')
                ->alwaysShow(),
            ShopEngineModel::make('Kondition', 'conditionSetVersionId')
                ->model(ConditionSet::class)
                ->valueFieldName('versionId')
                ->labelFieldName('name')
                ->required(true)->rules('required')
                ->onlyOnForms(),
            ShopEngineModel::make('Codepool', 'codepoolId')
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
}
