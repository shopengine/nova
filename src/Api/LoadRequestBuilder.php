<?php

namespace ShopEngine\Nova\Api;

use Illuminate\Support\Collection;
use Laravel\Nova\Http\Requests\NovaRequest;
use ShopEngine\Nova\Models\ShopEngineModel;
use ShopEngine\Nova\Structs\Api\LoadRequestStruct;

class LoadRequestBuilder extends RequestBuilder
{
    protected Collection $loadedEntities;

    protected ShopEngineModel $model;

    public function __construct(ShopEngineModel $model)
    {
        parent::__construct($model);
        $this->loadedEntities = new Collection();
    }

    /**
     * Shorthand Query
     * @param $id
     */
    public function find($id)
    {
        return $this->loadItem(new LoadRequestStruct($id));
    }

    /**
     * Execute the query statement on ShopEngine API.
     *
     * @param \ShopEngine\Nova\Structs\Api\LoadRequestStruct $loadRequestStruct
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Builder[]
     */
    public function loadItem(LoadRequestStruct $loadRequestStruct): ShopEngineModel
    {
        $rawResponse = $this->getClient()->get(
            $this->getEndpoint() . '/' . $loadRequestStruct->createApiRequest()
        );

        $entity = new $this->model($rawResponse);
        $this->loadedEntities->add($entity);
        return $entity;
    }

    /**
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     *
     * @return \ShopEngine\Nova\Structs\Api\LoadRequestStruct
     */
    public function buildFromRequest(NovaRequest $request): LoadRequestStruct
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
        // @todo spoof - fixe me?
        return $this;
    }
}
