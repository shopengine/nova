<?php

namespace ShopEngine\Nova\Api;

use ShopEngine\Nova\Models\ShopEngineModel;
use SSB\Api\Model\Code;

class StoreRequestBuilder extends RequestBuilder
{
    public function save(ShopEngineModel $model)
    {
        $seRequest = $this->buildFromModel($model);
        $this->getClient()->post($this->getEndpoint(), $seRequest);
    }

    public function buildFromModel(ShopEngineModel $model)
    {
        // todo: type this stuff!
        $seRequest = $model->getDirty();

        // todo: make this as event
        if (isset($seRequest['seRequest'])) {
            $jsonRequest = json_decode($seRequest['seRequest'], true);
            unset($seRequest['seRequest']);
            $seRequest = array_merge($jsonRequest, $seRequest);
        }

        if ($model->useSwaggerTypesOnUpsert) {
            $swaggerTypes = $model->model::swaggerTypes();

            foreach ($seRequest as $key => $value) {
                if (!isset($swaggerTypes[$key])) {
                    unset($seRequest[$key]);
                    continue;
                }

                $seRequest[$key] = $this->fixTypes($value, $swaggerTypes[$key]);
            }
        }


        // todo: make an listener / event for that
        if ($model->model instanceof Code &&
            property_exists($model, 'quantity') &&
            intval($model->quantity) > 1) {
            $seRequest['quantity'] = intval($model->quantity);
            $seRequest['code'] = '';
        }

        return $seRequest;
    }
}
