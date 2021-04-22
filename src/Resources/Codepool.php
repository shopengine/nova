<?php namespace Brainspin\Novashopengine\Resources;

use Brainspin\Novashopengine\Fields\CodepoolActions;
use Brainspin\Novashopengine\Fields\CodepoolCodes;
use Brainspin\Novashopengine\Fields\CodepoolStatistics;
use Brainspin\Novashopengine\Filter\CodepoolArchive;
use Brainspin\Novashopengine\Models\CodepoolModel;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Panel;

class Codepool extends ShopEngineResource
{
    public static $model = CodepoolModel::class;
    public static $title = 'orderId';
    public static $search = ['name'];

    public static $defaultSort = '-updatedAt';
    public static $id = 'id';

    public static function getShopEngineEndpoint(): string
    {
        return 'codepool';
    }

    public function fields(Request $request)
    {
        return [
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

            CodepoolStatistics::make('Statistiken')->onlyOnDetail(),

            new Panel('Weitere Aktionen', [
                CodepoolActions::make('Weitere Aktionen')->onlyOnDetail(),
            ]),

            new Panel('Codes', [
                CodepoolCodes::make('Codes', 'codes')
                    ->onlyOnDetail()
            ])
        ];
    }

    public function filters(Request $request)
    {
        return [
            new CodepoolArchive()
        ];
    }
}
