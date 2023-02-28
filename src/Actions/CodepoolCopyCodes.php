<?php

namespace ShopEngine\Nova\Actions;

use App\Models\Shop;
use App\Models\Shop as ShopModel;
use Epartment\NovaDependencyContainer\NovaDependencyContainer;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\ActionRequest;
use SSB\Api\Client;

class CodepoolCopyCodes extends Action
{
    use InteractsWithQueue;
    use Queueable;

    public $model;

    /**
     * The displayable name of the action.
     *
     * @var string
     */
    public $name = 'Codepool inkl. Codes kopieren';

    // TODO: Why did it not work? I need a Information for User with current condition
    public $confirmText = 'TEST';

    public $confirmButtonText = 'Codepool & Codes Kopieren';

    public $cancelButtonText = 'Abbrechen';

    public $onlyOnDetail = true;

    public Client $client;

    public ?int $modelId;

    public mixed $fromConditions;

    public function __construct($modelId)
    {
        $this->client         = \Shop::shopEngineClient();
        $this->modelId        = $modelId;
        $this->fromConditions = $this->client->get('codepool/conditions', [
            'codepoolId' => $this->modelId,
        ]);
    }

    /**
     * Perform the action on the given models.
     *
     * @param \Laravel\Nova\Http\Requests\ActionRequest $request
     *
     * @return mixed
     */
    public function handleRequest(ActionRequest $request)
    {
        $requestInputs = $request->collect();

        $fromCodepoolId = $requestInputs->get('resources');
        $toShopId       = $requestInputs->get('shopId');
        $toCodepoolId   = $requestInputs->get('codepoolId');

        // Todo: validate if condition select is empty -> don't close the modal -> error notification for required field
        $conditions   = $requestInputs->filter(fn($value, $key) => startsWith($key, 'condition_') && !empty($value));
        $conditionIds = $conditions->mapWithKeys(fn($value, $key) => [
            str_replace('condition_', '', $key) => $value,
        ]);

        if ($conditions->count() > 1) {
            // Todo: copy in a batch for any condition
        } else {
            // Todo: copy in one batch
        }

        Log::debug('Data', [
            $this->client->shop,
            $fromCodepoolId,
            $toShopId,
            $toCodepoolId,
            $conditionIds,
            $conditions->count(),
            $conditionIds->take(1)->keys()->first(),
        ]);

        /** @var Shop $destinationShop */
        $destinationShop = \Shop::find($toShopId);

        $destinationShopEngineClient = \Shop::shopEngineClient($destinationShop->settings);

        $destinationShopEngineClient->post('codepool/copyAdvanced', [
            'from_shop'                 => $this->client->shop,
            'from_codepool_id'          => $fromCodepoolId,
            'from_status'               => 'enabled',
            'condition_set_version_ids' => $conditionIds->toArray(),
            'status'                    => 'enabled',
            'validation'                => '',
            'to_codepool_id'            => $toCodepoolId,
            'note'                      => '',
            'hidden'                    => '',
            'from_usage_count'          => 1,
        ]);

        return Action::danger('Nothing to do.');

        // TODO: Check if destination Codepool is empty eg. add only new codes

        /*if ($resource !== Codepool::class) {
            return Action::danger($resource . ' is not ' . Codepool::class);
        }

        if (!is_numeric($fromCodepoolId)) {
            return Action::danger('Resource id is not numeric');
        }*/

        // Get codes from current shop
        /* $fromCodes = $this->client->get('code', [
             'codepoolId-eq' => $fromCodepoolId,
             'status-eq'     => 'enabled',
             'properties'    => 'code|status|conditionSetActive|validation|note|hidden',
         ]);

         $countFromCodes = count($fromCodes);*/

        /** @var Shop $destinationShop */
        /*$destinationShop = \Shop::find($toShopId);*/

        // TODO: Check if shopId is allowed

        /*$destinationShopEngineClient = \Shop::shopEngineClient($destinationShop->settings);

        $destinationShopEngineClient->post('codepool/copy', [
            'from_codepool_id'         => $fromCodepoolId,
            'from_status'              => 'enabled',
            'condition_set_version_id' => $toConditionId,
            'status'                   => 'enabled',
            'validation'               => '',
            'codepool_id'              => $toCodepoolId,
            'note'                     => '',
            'hidden'                   => '',
            'from_usage_count'         => '',
        ]);*/

        return true;
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [
            Heading::make('<b>Aktueller Shop: </b>' . \Shop::current()->name)->asHtml(),

            Select::make('Ziel Shop', 'shopId')
                ->options($this->getShopOptions())
                ->rules('required'),

            $this->getShopFieldContainer(1),
            $this->getShopFieldContainer(2),
            $this->getShopFieldContainer(3),
            $this->getShopFieldContainer(4),

            // Next step is to ask to build or select a codepool in destination shop.
        ];
    }

    protected function getShopFieldContainer($shopId): NovaDependencyContainer
    {
        $codepoolSelect = [
            Select::make('Ziel Codepool', 'codepoolId')
                ->searchable()
                ->options($this->getCodepoolOptions($shopId))
                ->rules('required'),
        ];

        if (count((array) $this->fromConditions) <= 5) {
            $conditionSelect = [];
            foreach ($this->fromConditions as $condition) {
                $conditionSelect[] = Select::make($condition->name, 'condition_' . $condition->id)
                    ->searchable()
                    ->options($this->getConditionOptions($shopId))
                    ->rules('required');
            }

            $fields = array_merge($codepoolSelect, $conditionSelect);
        } else {
            $fields = $codepoolSelect;
        }

        return NovaDependencyContainer::make($fields)->dependsOn('shopId', $shopId);
    }

    protected function getShopOptions(): array
    {
        $shops = \Shop::allShops()
            ->filter(fn(ShopModel $shop) => $shop->id !== \Shop::current()->id);

        $shopOptions = [];
        foreach ($shops as $shop) {
            $shopOptions[$shop->id] = $shop->name;
        }

        return $shopOptions;
    }

    protected function getConditionOptions(int $shopId): array
    {
        /** @var Shop $destinationShop */
        $destinationShop = \Shop::find($shopId);

        $destinationShopEngineClient = \Shop::shopEngineClient($destinationShop->settings);

        $conditions = $destinationShopEngineClient->get('conditionset', [
            'name-nlike' => 'Generated - %',
            'properties' => 'name|versionId',
        ]);

        $conditionOptions = [];
        foreach ($conditions as $condition) {
            $conditionOptions[$condition['versionId']] = $condition['name'];
        }

        return $conditionOptions;
    }

    protected function getCodepoolOptions(int $shopId): array
    {
        /** @var Shop $destinationShop */
        $destinationShop = \Shop::find($shopId);

        $destinationShopEngineClient = \Shop::shopEngineClient($destinationShop->settings);

        // Todo: maybe only empty codepools? asked in ticket and waiting for response
        $codepools = $destinationShopEngineClient->get('codepool', [
            'properties' => 'name|id',
        ]);

        $codepoolOptions = [];
        foreach ($codepools as $codepool) {
            $codepoolOptions[$codepool['id']] = $codepool['name'];
        }

        return $codepoolOptions;
    }
}
