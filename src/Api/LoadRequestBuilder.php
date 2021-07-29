<?php
namespace ShopEngine\Nova\Api;


use ShopEngine\Nova\Models\ShopEngineModel;
use ShopEngine\Nova\Structs\Api\LoadRequestStruct;

class LoadRequestBuilder extends RequestBuilder
{

    /**
     * Execute the query statement on ShopEngine API.
     *
     * @param \ShopEngine\Nova\Structs\Api\LoadRequestStruct $loadRequestStruct
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Builder[]
     */
    public function loadItem(LoadRequestStruct $loadRequestStruct) : ShopEngineModel
    {
        $rawResponse = $this->getClient()->get(
            $this->getShopEnginePath() . '/' . $loadRequestStruct->createApiRequest()
        );

        $modelClass = $this->request->resource()::getModel();
        return new $modelClass($rawResponse);
    }

    public function buildFromRequest() : LoadRequestStruct
    {
        // @todo investigate why named route properties are not defined
        // this is a interim solution
        $resourceId = $this->request->get('resourceId');

        if (!$resourceId) {
            $resourceId = $this->request->segment(3);
        }

        if (!$resourceId) {
            throwException(new \Exception('Missing Resource Id'));
        }

        return new LoadRequestStruct($resourceId);
    }
}
