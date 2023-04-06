<?php

namespace ShopEngine\Nova\Api;

use Exception;
use ShopEngine\Nova\Models\ShopEngineModel;
use SSB\Api\Model\Code;

class UpdateRequestBuilder extends RequestBuilder
{
    /**
     * @param ShopEngineModel $model
     * @return bool
     * @throws Exception
     */
    public function save(ShopEngineModel $model): bool
    {
        $parameters = $this->buildFromModel($model);
        $aggregateId = $this->request->resourceId;
        $endpoint = $this->getShopEnginePath() . '/' . $aggregateId;

        $this->getClient()->patch($endpoint, $parameters);

        return true;
    }

    public function buildFromModel(ShopEngineModel $model)
    {
        // todo: type this stuff!
        $seRequest = $model->getDirty();

        $swaggerTypes = $model->model::swaggerTypes();

        foreach ($seRequest as $key => $value) {
            if (!isset($swaggerTypes[$key])) {
                unset($seRequest[$key]);
                continue;
            }

            $seRequest[$key] = $this->fixTypes($value, $swaggerTypes[$key]);
        }

        if ($model->model instanceof Code &&
            property_exists($model, 'quantity') &&
            intval($model->quantity) > 1) {
            $seRequest['quantity'] = intval($model->quantity);
            $seRequest['code'] = '';
        }

        return $seRequest;
    }
}
