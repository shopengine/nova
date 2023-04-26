<?php

namespace ShopEngine\Nova\Api;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use ShopEngine\Nova\Models\ShopEngineModel;
use ShopEngine\Nova\Structs\Api\LoadRequestStruct;

class LoadRequestBuilder extends RequestBuilder
{
    /**
     * Execute the query statement on ShopEngine API.
     *
     * @param LoadRequestStruct $loadRequestStruct
     * @return Collection|Builder[]
     * @throws Exception
     */
    public function loadItem(LoadRequestStruct $loadRequestStruct): ShopEngineModel
    {
        $rawResponse = $this->getClient()->get(
            $this->getShopEnginePath() . '/' . $loadRequestStruct->createApiRequest()
        );

        $modelClass = $this->request->resource()::getModel();
        return new $modelClass($rawResponse);
    }

    /**
     * @throws Exception
     */
    public function buildFromRequest(): LoadRequestStruct
    {
        // @todo investigate why named route properties are not defined
        // this is a interim solution
        $resourceId = $this->request->get('resourceId');

        if (!$resourceId) {
            $resourceId = $this->request->segment(3);
        }

        if (!$resourceId) {
            throw new Exception('Missing Resource Id');
        }

        return new LoadRequestStruct($resourceId);
    }
}
