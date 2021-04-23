<?php
namespace Brainspin\Novashopengine\Api;


use Brainspin\Novashopengine\Codepools\Resources\Codepool;
use Brainspin\Novashopengine\Models\ShopEngineModel;
use Laravel\Nova\Http\Requests\NovaRequest;
use SSB\Api\Model\Code;

class StoreRequestBuilder extends RequestBuilder
{

    public function save(ShopEngineModel $model) {
        $seRequest = $this->buildShopEngineRequest($model);
        $rawResponse = $this->getClient()->post($this->getShopEnginePath(), $seRequest);
    }

    private function buildShopEngineRequest(ShopEngineModel $model)
    {
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
