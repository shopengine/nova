<?php

namespace Brainspin\Novashopengine\Http\Controllers;

use Brainspin\Novashopengine\Models\ShopEngineModel;
use Brainspin\Novashopengine\Resources\CodepoolGroup;
use Brainspin\Novashopengine\Resources\Purchase;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\FilterDecoder;
use Laravel\Nova\Http\Requests\NovaRequest;

class IndexController extends ShopEngineNovaController
{
    public function index(NovaRequest $request)
    {
        /** @var \Brainspin\Novashopengine\Resources\ShopEngineResource $resource */
        $resource = $request->resource();

        if ($resource === CodepoolGroup::class) {
            /** @var JsonResponse $response */
            $response = app()->call('Laravel\Nova\Http\Controllers\ResourceIndexController@handle');

            $data = $response->getOriginalContent();
            $data['shopSettings'] = $this->getShopSettings()->toArray();
            $response->setData($data);

            return $response;
        }

        $shopEnginePath = $resource::getShopEngineEndpoint();
        $seRequest = $request->all();

        $sort = false;
        $sortDesc = false;

        if (isset($seRequest["{$resource::uriKey()}_order"])) {
            $sort = $seRequest["{$resource::uriKey()}_order"];
            unset($seRequest["{$resource::uriKey()}_order"]);
        }

        if (isset($seRequest["{$resource::uriKey()}_direction"])) {
            $sortDesc = $seRequest["{$resource::uriKey()}_direction"] === 'desc';
            unset($seRequest["{$resource::uriKey()}_direction"]);
        }

        if (isset($seRequest['search'])) {
            $searchAttribute = $resource::$search[0] ?? false;
            if ($searchAttribute) {
                $seRequest["{$searchAttribute}-like"] = urlencode("%{$seRequest['search']}%");
            }
            unset($seRequest['search']);
        }

        if (isset($seRequest["{$resource::uriKey()}_filter"])) {
            $filter = $seRequest["{$resource::uriKey()}_filter"];
            unset($seRequest["{$resource::uriKey()}_filter"]);

            $filters = (new FilterDecoder($filter, $request->newResource()->availableFilters($request)))->filters();
            foreach ($filters as $applyFilterObj) {
                $seRequest = $applyFilterObj->filter->apply($request, $seRequest, $applyFilterObj->value);
            }
        }

        $seRequestCount = $seRequest;
        unset($seRequestCount['page']);
        unset($seRequestCount['pageSize']);

        if ($sort) {
            $seRequest['sort'] = ($sortDesc ? '-' : '') . $sort;
        }
        else {
            $seRequest['sort'] = $resource::$defaultSort;
        }

        if ($resource === Purchase::class) {
            // todo: remove fixed strings
            $seRequest['email-ne'] = 'login@brainspin.de';
        }

        $indexFields = $request->newResource()->indexFields($request);

        $seRequest['properties'] = $indexFields->map(fn(Field $field) => $field->attribute)
            ->add($resource::$id)
            ->join('|');

        $client = $this->getClient();
        $count = $client->get($shopEnginePath . '/count', $seRequestCount);
        $rawResponse = $client->get($shopEnginePath, $seRequest);

        $resources = collect($rawResponse)->map(function ($seModel) {
            return new ShopEngineModel($seModel);
        })->mapInto($resource)->map->serializeForIndex($request);

        return response()->json([
            'label' => $resource::label(),
            'resources' => $resources,
            'prev_page_url' => null,
            'next_page_url' => null,
            'per_page' => $seRequest['pageSize'] ?? count($resources),
            'per_page_options' => $resource::perPageOptions(),
            'softDeletes' => false,

            'count' => $count,
            'defaultSort' => $resource::$defaultSort,

            'shopSettings' => $this->getShopSettings()->toArray()
        ]);
    }

    public function filter(NovaRequest $request)
    {
        return response()->json($request->newResource()->availableFilters($request));
    }

    public function lastCodes(NovaRequest $request)
    {
        $seRequest = $request->all();

        $days = 30;

        $minOrderDate = date('Y-m-d', strtotime('-'.$days.' days'));

        $shopSettingSlug = $this->getShopSettings()->getSlug();

        $total = DB::select("
            SELECT pcp.name, pc.code, p.grand_total, p.order_id, p.order_date
            FROM stats_purchase_codes AS pc
            JOIN stats_codepools AS pcp ON pcp.id = pc.codepool_id
            JOIN stats_purchases AS p ON pc.purchase_id = p.id
            WHERE p.order_date > '$minOrderDate'
            AND p.shop_setting_slug = '$shopSettingSlug'
            ORDER BY p.order_date DESC
        ");

        $orders = [];

        foreach ($total as $t) {
            if (!isset($orders[$t->order_id])) {
                $orders[$t->order_id] = [
                    'codes' => [],
                    'order_id' => $t->order_id,
                    'grand_total' => $t->grand_total,
                    'order_date' => $t->order_date,
                ];
            }

            $orders[$t->order_id]['codes'][] = [
                'campaign' => $t->name,
                'code' => $t->code,
            ];
        }

        $totalsByCode = [];
        $codeToCampaign = [];

        foreach ($orders as $o) {
            foreach ($o['codes'] as $c) {
                if (
                    $c['campaign'] == 'Newsletter Geschenk' ||
                    $c['campaign'] == 'Newsletter Geschenk Checkout'
                ) {
                    continue;
                }

                if (!isset($totalsByCode[$c['code']])) {
                    $totalsByCode[$c['code']] = 0;
                }
                $totalsByCode[$c['code']] += 1;
                $codeToCampaign[$c['code']] = $c['campaign'];
            }
        }

        $totalsByCode = array_filter($totalsByCode, function ($codeUsage) {
            return $codeUsage > 1;
        });

        arsort($totalsByCode);

        $list = [];

        foreach ($totalsByCode as $key => $value) {
            $list[] = [
                'id' => [
                    "attribute" => "id",
                    "component" => "id-field",
                    "helpText" => null,
                    "indexName" => "ID",
                    "name" => "ID",
                    "nullable" => false,
                    "panel" => null,
                    "prefixComponent" => true,
                    "readonly" => true,
                    "required" => false,
                    "sortable" => false,
                    "sortableUriKey" => "id",
                    "stacked" => false,
                    "textAlign" => "left",
                    "validationKey" => "aggregateId",
                    "value" => null
                ],
                'fields' => [
                    [
                        "attribute" => "id",
                        "component" => "text-field",
                        "helpText" => null,
                        "indexName" => "Marketing Kampagne",
                        "name" => "Marketing Kampagne",
                        "nullable" => false,
                        "panel" => null,
                        "prefixComponent" => true,
                        "readonly" => true,
                        "required" => false,
                        "sortable" => false,
                        "sortableUriKey" => "id",
                        "stacked" => false,
                        "textAlign" => "left",
                        "validationKey" => "aggregateId",
                        "value" => $codeToCampaign[$key] . " ($key)"
                    ],
                    [
                        "attribute" => "id",
                        "component" => "text-field",
                        "helpText" => null,
                        "indexName" => "Code Einlösungen",
                        "name" => "Code Einlösungen",
                        "nullable" => false,
                        "panel" => null,
                        "prefixComponent" => true,
                        "readonly" => true,
                        "required" => false,
                        "sortable" => false,
                        "sortableUriKey" => "id",
                        "stacked" => false,
                        "textAlign" => "left",
                        "validationKey" => "aggregateId",
                        "value" => $value
                    ]
                ],
                'authorizedToView' => false
            ];
        }


        $orderCount = count($orders);

        $totalCount = count($list);

        $list = array_slice($list, $seRequest['pageSize'] * $seRequest['page'], $seRequest['pageSize']);

        return [
            'label' => "Bestellungen der letzten $days Tage ($orderCount)",
            'resources' => $list,
            'prev_page_url' => null,
            'next_page_url' => null,
            'per_page' => $seRequest['pageSize'] ?? count($list),
            'per_page_options' => [],
            'softDeletes' => false,

            'count' => $totalCount,
            'defaultSort' => '',

            'shopSettings' => $this->getShopSettings()->toArray(),
            'isSearchable' => false
        ];
    }
}
