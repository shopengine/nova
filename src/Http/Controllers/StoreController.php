<?php

namespace Brainspin\Novashopengine\Http\Controllers;

use Brainspin\Novashopengine\Models\CodepoolModel;
use Brainspin\Novashopengine\Models\ShopEngineModel;
use Brainspin\Novashopengine\Resources\Codepool;
use Laravel\Nova\Http\Requests\NovaRequest;

class StoreController extends ShopEngineNovaController
{
    public function store(string $resource, NovaRequest $request)
    {
        $resource = $request->resource();

        // todo: refactor that extra case
        if ($resource === \Brainspin\Novashopengine\Resources\CodepoolGroup::class) {
            $response = app()->call('Laravel\Nova\Http\Controllers\ResourceStoreController@handle');
            $data = $response->getOriginalContent();
            $data['redirect'] = '/novashopengine/codepool-groups/' . $data['resource']['id'];
            $response->setData($data);
            return $response;
        }

        $resource::authorizeToCreate($request);
        $resource::validateForCreation($request);

        [$model, $callbacks] = $resource::fill(
            $request, $resource::newModel()
        );

        $seRequest = $this->makeSeRequest($model);

        collect($callbacks)->each->__invoke();
        $client = $this->getClient();
        $seResponse = $client->post($resource::$shopEnginePath, $seRequest);

        if (is_array($seResponse)) {
            // An empty array indicates the edge case of mass code creation.
            // Therefore no ShopEngine Model is returned within the array.
            // Instead we prepare to redirect to the codepool for which the codes are created.
            if (empty($seResponse)) {
                $seResponse = $client->get(Codepool::$shopEnginePath . '/' . $seRequest['codepoolId']);
            }
            else {
                $seResponse = $seResponse[0];
            }
        }

        if (!($seResponse instanceof \SSB\Api\Model\ModelInterface)) {
            abort(500, $seResponse->msg ?? 'Unknown Error');
        }

        $model = new ShopEngineModel($seResponse);
        $resource = $request->resource();
        $resourceInstance = new $resource($model);

        return response()->json([
            'id' => $resourceInstance->getKey(),
            'resource' => $model->attributesToArray(),
            'redirect' => $resource::redirectAfterCreate($request, $resourceInstance),
        ], 201);
    }
}
