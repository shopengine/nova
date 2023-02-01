<?php

namespace ShopEngine\Nova\Actions;

use App\Models\Shop;
use App\Models\Shop as ShopModel;
use Epartment\NovaDependencyContainer\NovaDependencyContainer;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Cards\Help;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\ActionRequest;
use ShopEngine\Nova\Resources\Codepool;
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
        $resource       = $request->resource();
        $fromCodepoolId = $request->input('resources');
        $toShopId       = $request->input('shopId');
        $toConditionId  = $request->input('conditionId');
        $toCodepoolId   = $request->input('codepoolId');

        // TODO: Check if destination Codepool is empty eg. add only new codes

        if ($resource !== Codepool::class) {
            return Action::danger($resource . ' is not ' . Codepool::class);
        }

        if (!is_numeric($fromCodepoolId)) {
            return Action::danger('Resource id is not numeric');
        }

        // Get codes from current shop
        /* $fromCodes = $this->client->get('code', [
             'codepoolId-eq' => $fromCodepoolId,
             'status-eq'     => 'enabled',
             'properties'    => 'code|status|conditionSetActive|validation|note|hidden',
         ]);

         $countFromCodes = count($fromCodes);*/

        /** @var Shop $destinationShop */
        $destinationShop = \Shop::find($toShopId);

        // TODO: Check if shopId is allowed

        $destinationShopEngineClient = \Shop::shopEngineClient($destinationShop->settings);

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
        ]);

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
        $conditionSelect = [
            Select::make('Ziel Codepool', 'codepoolId')
                ->searchable()
                ->options($this->getCodepoolOptions($shopId))
                ->rules('required'),
        ];

        if (count((array) $this->fromConditions) < 5) {
            $conditionSelect2 = [];

            foreach ($this->fromConditions as $condition) {
                Log::debug('B', [$condition->name, $condition->id]);
                $conditionSelect2[] = Select::make($condition->name, 'condition_'.$condition->id)
                    ->searchable()
                    ->options($this->getConditionOptions($shopId))
                    ->rules('required');
            }

            $new = array_merge($conditionSelect, $conditionSelect2);
        } else {
            $new = $conditionSelect;
        }



        Log::debug('A', [$new]);

        /*foreach ($this->fromConditions as $condition) {
            Select::make($condition->name, $condition->id)
                ->searchable()
                ->options($this->getConditionOptions($shopId))
                ->rules('required')
        }

        Log::debug('B', [count($conditionSelect)]);*/

        /*$fields = array_merge([
            Select::make('Ziel Codepool', 'codepoolId')
                ->searchable()
                ->options($this->getCodepoolOptions($shopId))
                ->rules('required')
        ], $conditionSelect);*/

        /*$fields = array_merge($conditionSelect, [
            Select::make('Ziel Codepool', 'codepoolId')
                ->searchable()
                ->options($this->getCodepoolOptions($shopId))
                ->rules('required')
        ]);*/

        return NovaDependencyContainer::make($new)->dependsOn('shopId', $shopId);
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

    protected function getConditionOptions($shopId = 1): array
    {
        /** @var Shop $destinationShop */
        $destinationShop = \Shop::find($shopId);

        // TODO: Check if shopId is allowed

        $destinationShopEngineClient = \Shop::shopEngineClient($destinationShop->settings);

        $conditions = $destinationShopEngineClient->get('conditionset', [
            'name-nlike' => 'Generated - %',
            'properties' => 'name|versionId',
        ]);

        Log::debug('TEST', [
            $this->modelId,
            $destinationShopEngineClient->post('codepool/copyAdvanced', []),
        ]);

        $conditionOptions = [];
        foreach ($conditions as $condition) {
            $conditionOptions[$condition['versionId']] = $condition['name'];
        }

        return $conditionOptions;
    }

    protected function getCodepoolOptions($shopId = 1): array
    {
        /** @var Shop $destinationShop */
        $destinationShop = \Shop::find($shopId);

        // TODO: Check if shopId is allowed

        $destinationShopEngineClient = \Shop::shopEngineClient($destinationShop->settings);

        $codepools = $destinationShopEngineClient->get('codepool', [
            'properties' => 'name|id',
        ]);

        $codepoolOptions = [];
        foreach ($codepools as $codepool) {
            $codepoolOptions[$codepool['id']] = $codepool['name'];
        }

        return $codepoolOptions;
    }

    /*protected function getCodes(ActionRequest $request): array
    {
        $codes = explode(',', $request->input('codes'));
        $codes = array_map('trim', $codes);
        $codes = array_filter($codes, fn(string $code) => ! Str::contains($code, ' '));

        return array_filter($codes);
    }*/
    /*protected function updateCodes(array $codes, int $codepoolId): array
    {
        $responseMessage = [];
        $codeAggregateIds = [];

        foreach (array_chunk($codes, 150) as $chunk) {

            $shopEngineCodes = $this->client->get('code', [
                'code-eq' => implode('|', $chunk),
                'codepoolId-ne' => $codepoolId,
                'properties' => 'aggregateId'
            ]);

            foreach ($shopEngineCodes as $code) {
                $codeAggregateIds[] = $code->getAggregateId();
            }
        }

        $responseMessage[] = 'Requested ' . count($codeAggregateIds) . ' codes';

        if (!empty($codeAggregateIds)) {
            $this->client->patch('code/updateBatch', [
                'aggregateIds' => implode('|', $codeAggregateIds),
                'data' => [
                    'codepoolId' => $codepoolId,
                ],
            ]);
        }

        return $responseMessage;
    }*/
}
