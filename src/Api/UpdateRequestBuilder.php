<?php
namespace ShopEngine\Nova\Api;


use ShopEngine\Nova\Models\ShopEngineModel;
use SSB\Api\Model\Code;

class UpdateRequestBuilder extends RequestBuilder
{

    public function save(ShopEngineModel $model) {
        $seRequest = $this->buildFromModel($model);
        $this->getClient()->patch(
            $this->getEndpoint().'/'.$model->getId(),
            $seRequest
        );
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
