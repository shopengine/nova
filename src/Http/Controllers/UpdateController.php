<?php

namespace Brainspin\Novashopengine\Http\Controllers;

use App\Models\ShopSetting;
use App\Models\StatsPurchaseCode;
use Brainspin\Novashopengine\Models\ShopEngineModel;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Http\Requests\NovaRequest;
use ShopEngineApiClient;

class UpdateController extends ShopEngineNovaController
{
    public function store(string $resource, string $resourceId, NovaRequest $request)
    {
        $requestResource = $request->resource();

        if ($requestResource === \Brainspin\Novashopengine\Resources\CodepoolGroup::class) {
            $response = app()->call('Laravel\Nova\Http\Controllers\ResourceUpdateController@handle');
            $data = $response->getOriginalContent();
            $data['redirect'] = '/novashopengine/codepool-groups/' . $data['resource']['id'];
            $response->setData($data);
            return $response;
        }

        $shopEnginePath = $requestResource::$shopEnginePath;

        $client = $this->getClient();
        $rawResponse = $client->get("$shopEnginePath/$resourceId");
        $seModel = new ShopEngineModel($rawResponse);
        $resource = new $requestResource($seModel);

        $resource->authorizeToUpdate($request);
        $resource::validateForUpdate($request, $resource);

        [$seModel, $callbacks] = $resource::fillForUpdate($request, $seModel);

        $seRequest = $this->makeSeRequest($seModel);

        collect($callbacks)->each->__invoke();

        $seResponse = $client->patch("$shopEnginePath/$resourceId", $seRequest);

        if (!($seResponse instanceof \SSB\Api\Model\ModelInterface)) {
            abort(500, $seResponse->msg ?? 'Unknown Error');
        }

        // update codepool_id in stats on codepoolchange in codes
        if (
            $requestResource === \Brainspin\Novashopengine\Resources\Code::class &&
            isset($seRequest['codepoolId']) &&
            $rawResponse->getCodepoolId() !== $seRequest['codepoolId']
        ) {
            $shop_setting_slug = $this->getShopSettings()->getSlug();

            DB::table('stats_purchase_codes')
                ->where('code', $seRequest['code'])
                ->whereRaw("EXISTS (SELECT * FROM stats_purchases WHERE stats_purchase_codes.purchase_id = stats_purchases.id AND stats_purchases.shop_setting_slug = '$shop_setting_slug')")
                ->update(['codepool_id' => $seRequest['codepoolId']]);
        }

        $model = new ShopEngineModel($seResponse);
        $resource = $request->resource();
        $resourceInstance = new $resource($model);

        return response()->json([
            'id' => $resourceInstance->getKey(),
            'resource' => $model->attributesToArray(),
            'redirect' => $resource::redirectAfterUpdate($request, $resourceInstance),
        ]);
    }

    public function fields(string $resource, string $resourceId, NovaRequest $request)
    {
        $resource = $request->resource();

        if ($resource === \Brainspin\Novashopengine\Resources\CodepoolGroup::class) {
            return app()->call('Laravel\Nova\Http\Controllers\UpdateFieldController@index');
        }

        $shopEnginePath = $resource::$shopEnginePath;

        $rawResponse = $this->getClient()->get("$shopEnginePath/$resourceId");
        $seModel = new ShopEngineModel($rawResponse);
        $resource = new $resource($seModel);

        $resource->authorizeToUpdate($request);

        return response()->json([
            'fields' => $resource->updateFieldsWithinPanels($request),
            'panels' => $resource->availablePanelsForUpdate($request),
        ]);
    }
}
