<?php

namespace ShopEngine\Nova\Actions;

use App\Models\Shop;
use Epartment\NovaDependencyContainer\NovaDependencyContainer;
use Illuminate\Support\Facades\Validator;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\ActionRequest;
use SSB\Api\Client;

class CodepoolCopyCodes extends Action
{
    public $name = 'Codes in anderen Shop kopieren.';

    public $confirmButtonText = 'Codes kopieren';

    public $cancelButtonText = 'Abbrechen';

    public $onlyOnDetail = true;

    public Client $client;

    public ?int $modelId;

    public mixed $fromConditions;

    public function __construct(?int $modelId)
    {
        $this->client = \Shop::shopEngineClient();

        if (is_null($modelId)) {
            $this->modelId        = null;
            $this->fromConditions = [];
        } else {
            $this->modelId        = $modelId;
            $this->fromConditions = $this->client->get('codepool/conditions', [
                'codepoolId' => $this->modelId,
            ]);
        }
    }

    public function handleRequest(ActionRequest $request): array
    {
        $requestInputs = $request->collect();

        $fromCodepoolId = $requestInputs->get('resources');
        $toShopId       = $requestInputs->get('shopId');
        $toCodepoolId   = $requestInputs->get('codepoolId');
        $conditions     = $requestInputs->filter(fn($value, $key) => startsWith($key, 'condition_'));

        $conditionIds = $conditions->mapWithKeys(fn($value, $key) => [
            str_replace('condition_', '', $key) => $value,
        ]);

        Validator::make($request->all(), $conditions->map(fn() => 'required')->toArray())->validate();

        /** @var Shop $destinationShop */
        $destinationShop = \Shop::find($toShopId);

        $destinationShopEngineClient = \Shop::shopEngineClient($destinationShop->settings);

        $response = $destinationShopEngineClient->post('codepool/copy-advanced', [
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

        return Action::danger($response->msg);
    }

    public function fields(): array
    {
        return [
            Heading::make('<b>Aktueller Shop:</b> ' . \Shop::current()->name)->asHtml(),

            Select::make('Ziel Shop', 'shopId')
                ->options($this->getShopOptions())
                ->rules('required'),

            $this->getShopFieldContainer(1),
            $this->getShopFieldContainer(2),
            $this->getShopFieldContainer(3),
            $this->getShopFieldContainer(4),
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
        $shops = \Shop::allShops()->filter(fn(Shop $shop) => $shop->id !== \Shop::current()->id);

        $shopOptions = [];
        foreach ($shops as $shop) {
            $shopOptions[$shop->id] = $shop->name;
        }

        return $shopOptions;
    }

    protected function getCodepoolOptions(int $shopId): array
    {
        /** @var Shop $destinationShop */
        $destinationShop = \Shop::find($shopId);

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
}
