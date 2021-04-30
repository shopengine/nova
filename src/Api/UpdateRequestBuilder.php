<?php
namespace Brainspin\Novashopengine\Api;


use Brainspin\Novashopengine\Models\ShopEngineModel;
use SSB\Api\Model\Code;

class UpdateRequestBuilder extends RequestBuilder
{

    public function save(ShopEngineModel $model) {
        $seRequest = $this->buildFromModel($model);
        $this->getClient()->patch($this->getShopEnginePath().'/'.$this->request->resourceId, $seRequest);
    }

    public function buildFromModel(ShopEngineModel $model)
    {
        // todo: type this stuff!
        $seRequest = collect(get_object_vars($model))
            ->filter(function ($a, $key) {
                return $key !== 'model';
            })
            ->toArray();

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
