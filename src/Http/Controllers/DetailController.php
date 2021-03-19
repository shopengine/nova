<?php

namespace Brainspin\Novashopengine\Http\Controllers;

use Brainspin\Novashopengine\Models\ShopEngineModel;
use Brainspin\Novashopengine\Resources\CodepoolGroup;
use Illuminate\Http\JsonResponse;
use Laravel\Nova\Http\Requests\NovaRequest;
use ShopEngineApiClient;

class DetailController extends ShopEngineNovaController
{
    public function show(string $resource, string $resourceId, NovaRequest $request)
    {
        $resource = $request->resource();

        if ($resource === CodepoolGroup::class) {
            /** @var JsonResponse $response */
            $response = app()->call('Laravel\Nova\Http\Controllers\ResourceShowController@handle');

            $data = $response->getOriginalContent();
            $data['seModel'] = [];
            $response->setData($data);

            return $response;
        }

        $shopEnginePath = $resource::$shopEnginePath;

        $client = $this->getClient();
        $rawResponse = $client->get("$shopEnginePath/$resourceId");

        $seModel = new ShopEngineModel($rawResponse);
        $resource = new $resource($seModel);

        $resource->authorizeToView($request);

        return response()->json([
            'panels' => $resource->availablePanelsForDetail($request, $resource),
            'resource' => $resource->serializeForDetail($request, $resource),
            'seModel' => $seModel
        ]);
    }
}
