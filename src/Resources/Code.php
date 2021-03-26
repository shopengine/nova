<?php
namespace Brainspin\Novashopengine\Resources;

use App\Models\StatsPurchaseCode;
use Brainspin\Novashopengine\Fields\CodepoolActions;
use Brainspin\Novashopengine\Fields\CodepoolLink;
use Brainspin\Novashopengine\Fields\CodeStatistics;
use Brainspin\Novashopengine\Fields\CodeValidation;
use Brainspin\Novashopengine\Fields\ShopEngineModel;
use Brainspin\Novashopengine\Filter\ActiveCodes;
use Brainspin\Novashopengine\Models\CodeModel;
use Brainspin\Novashopengine\Services\ConfiguredClassFactory;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Panel;

class Code extends ShopEngineResource
{
    public static $model = CodeModel::class;
    public static $title = 'code';
    public static $search = ['code'];

    public static $shopEnginePath = 'code';
    public static $defaultSort = '-updatedAt';
    public static $id = 'aggregateId';

    public function fields(Request $request)
    {
        $shopService = ConfiguredClassFactory::getShopEngineService();
        $shop_setting_slug = $shopService->shopRegion();

        return [
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
            ])->onlyOnForms()->required(true)->rules('required'),

            Date::make('Erstellt am', 'createdAt')
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->sortable(true),
            Date::make('Aktualisiert am', 'updatedAt')
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->sortable(true),

            CodeStatistics::make('Benutzungen', function() use ($shop_setting_slug) {
                if ($this->model->getCode() === null) {
                    return '0';
                }

                return StatsPurchaseCode::getDefaultStatsAsString($this->model->getCode(), $shop_setting_slug);
            }),

            Text::make('Marketing Kampagne', 'codepoolName')
                ->onlyOnIndex(),

            CodepoolLink::make('Marketing Kampagne', 'codepoolId')
                ->onlyOnDetail(),

            Text::make('Kondition', 'conditionSetName')
                ->hideWhenCreating()
                ->hideWhenUpdating(),
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

            new Panel('Weitere Aktionen', [
                CodepoolActions::make('Weitere Aktionen')->onlyOnDetail(),
            ]),
        ];
    }

    public function filters(Request $request)
    {
        return [
            new ActiveCodes()
        ];
    }
}
