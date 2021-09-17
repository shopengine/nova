<?php
namespace ShopEngine\Nova\Api;

use Illuminate\Support\Collection;
use Laravel\Nova\Http\Requests\NovaRequest;
use ShopEngine\Nova\Models\ShopEngineModel;
use ShopEngine\Nova\Structs\Api\LoadRequestStruct;

class LoadRequestBuilder extends RequestBuilder
{
    protected Collection $loadedEntities;

    /**
     * @param \Illuminate\Support\Collection $loadedEntities
     */
    public function __construct(string $resource)
    {
        parent::__construct($resource);
        $this->loadedEntities = new Collection();
    }


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

        $modelClass = $this->resource::getModel();
        $entity = new $modelClass($rawResponse);
        $this->loadedEntities->add($entity);
        return $entity;
    }

    public function buildFromRequest(NovaRequest $request) : LoadRequestStruct
    {
        // @todo investigate why named route properties are not defined
        // this is a interim solution
        $resourceId = $request->get('resourceId');

        if (!$resourceId) {
            $resourceId = $request->segment(3);
        }

        if (!$resourceId) {
            throwException(new \Exception('Missing Resource Id'));
        }

        return new LoadRequestStruct($resourceId);
    }

    public function whereKey($ids)
    {
        if (empty($ids)) {
            throw new \Exception('missing entity ids');
        }

        $request = new LoadRequestStruct($ids[0]);
        return $this->loadItem($request);
    }

    public function latest($column)
    {
        return $this->loadedEntities->last();
    }


    /**
     * Chunk the collection into chunks of the given size.
     *
     * @param  int  $size
     * @return static
     */
    public function chunk($size)
    {
     //   dd((new \Exception('x'))->getTraceAsString());
        return $this;
    }
}
