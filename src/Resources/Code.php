<?php

namespace ShopEngine\Nova\Resources;

use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Heading;
use ShopEngine\Nova\Fields\CodepoolLink;
use ShopEngine\Nova\Fields\CodeValidation;
use ShopEngine\Nova\Fields\ShopEngineModel;
use ShopEngine\Nova\Fields\Toggle;
use ShopEngine\Nova\Filter\ActiveCodes;
use ShopEngine\Nova\Models\CodeModel;
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

    public static $defaultSort = '-createdAt';
    public static $id = 'aggregateId';


    public static function getModel() : string
    {
        return CodeModel::class;
    }

    public static function getShopEngineEndpoint(): string
    {
        return 'code';
    }

    public static function label()
    {
        return __('se.codes');
    }

    public static function singularLabel()
    {
        return __('se.code');
    }

    public function fields(Request $request)
    {
        return $this->appendShopEngineFields([
            Text::make(__('se.code'), 'code'),
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
            Text::make(__('se.codepool'), 'codepoolName')
                ->onlyOnIndex(),
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
            CodepoolLink::make(__('se.codepool'), 'codepoolId')
                ->onlyOnDetail(),
            Textarea::make('Notiz', 'note')
                ->alwaysShow(),
            Text::make(__('se.conditionset'), function ($resource) {
                return $this->model ? $this->model->getConditionSetName() : null;
            })->onlyOnDetail(),
            ShopEngineModel::make(__('se.conditionset'), 'conditionSetVersionId')
                ->model(ConditionSet::class)
                ->valueFieldName('versionId')
                ->labelFieldName('name')
                ->required(true)
                ->rules('required')
                ->onlyOnForms(),
            ShopEngineModel::make(__('se.codepool'), 'codepoolId')
                ->model(Codepool::class)
                ->labelFieldName('name')
                ->required(true)->rules('required')
                ->onlyOnForms(),
            Boolean::make('Versteckt', 'hidden')
                ->hideFromIndex(),
            CodeValidation::make('Validierungen', 'validation')
                ->hideFromIndex(),
            Heading::make('Guthaben-Aufladung'),
            Number::make('Wert in Cent', 'rechargeAmount')
                ->nullable()
                ->rules('required_unless:rechargeType,null|required_unless:rechargeFrequency,null|required_unless:rechargeAt,null')
                ->min(0)
                ->step(1)
                ->resolveUsing(function ($value) {
                    return empty($value) || $value <= 0 ? null : $value;
                })
                ->hideFromIndex(),
            Select::make('Typ', 'rechargeType')
                ->nullable()
                ->rules('required_unless:rechargeAmount,null|required_unless:rechargeFrequency,null|required_unless:rechargeAt,null')
                ->options([
                    'absolute' => 'Setzt das Guthaben auf den angegeben Wert',
                    'relative' => 'Addiert den angegeben Wert mit dem verbleibenden Guthaben',
                ])
                ->displayUsingLabels()
                ->hideFromIndex(),
            Select::make('Häufigkeit', 'rechargeFrequency')
                ->nullable()
                ->rules('required_unless:rechargeAmount,null|required_unless:rechargeType,null|required_unless:rechargeAt,null')
                ->options([
                    'monthly' => 'Monatlich',
                ])
                ->displayUsingLabels()
                ->hideFromIndex(),
            Toggle::make('Status', 'status')
                ->withMeta([
                    'singularResourceName' => 'code',
                    'aggregateId' => $this->attributes('aggregateId')->data['aggregateId']
                ])->onlyOnIndex(),
            DateTime::make('Nächster Termin', 'rechargeAt')
                ->nullable()
                ->rules('required_unless:rechargeAmount,null|required_unless:rechargeType,null|required_unless:rechargeFrequency,null')
                ->hideFromIndex()
        ]);
    }

    public function filters(Request $request)
    {
        return [
            new ActiveCodes()
        ];
    }
}
