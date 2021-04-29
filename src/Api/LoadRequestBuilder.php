<?php
namespace Brainspin\Novashopengine\Api;


use Brainspin\Novashopengine\Models\ShopEngineModel;
use Brainspin\Novashopengine\Resources\Purchase;
use Brainspin\Novashopengine\Structs\Api\ListRequestStruct;
use Brainspin\Novashopengine\Structs\Api\LoadRequestStruct;
use Brainspin\Novashopengine\Structs\Api\RequestFilterStruct;
use Illuminate\Container\Container;
use Illuminate\Pagination\Paginator;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\FilterDecoder;
use Laravel\Nova\Http\Requests\NovaRequest;

class LoadRequestBuilder extends RequestBuilder
{

    /**
     * Execute the query statement on ShopEngine API.
     *
     * @param \Brainspin\Novashopengine\Structs\Api\LoadRequestStruct $loadRequestStruct
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Builder[]
     */
    public function loadItem(LoadRequestStruct $loadRequestStruct) : ShopEngineModel
    {
        $rawResponse = $this->getClient()->get(
            $this->getShopEnginePath() . '/' . $loadRequestStruct->createApiRequest()
        );

        return new ShopEngineModel($rawResponse);
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
