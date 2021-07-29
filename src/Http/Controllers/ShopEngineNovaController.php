<?php

namespace ShopEngine\Nova\Http\Controllers;

use ShopEngine\Nova\Contracts\NovaShopEngineInterface;
use ShopEngine\Nova\Services\ConfiguredClassFactory;
use Illuminate\Routing\Controller;
use SSB\Api\Client;
use SSB\Api\Contracts\ShopEngineSettingsInterface;
use SSB\Api\Model\Code;

class ShopEngineNovaController extends Controller
{
    /** @var NovaShopEngineInterface */
    protected $shopService;

    public function __construct()
    {
        $this->shopService =  ConfiguredClassFactory::getShopEngineService();
    }

    protected function getShopSettings(): ShopEngineSettingsInterface
    {
        return $this->shopService->shopEngineSettings();
    }

    protected function getClient(): Client
    {
        return $this->shopService->shopEngineClient($this->getShopSettings());
    }

    protected function fixTypes($value, string $type)
    {
        switch ($type) {
            case 'string':
                return "$value";
            case 'int':
                return intval($value);
            case 'bool':
                return $value == true;
            case '\SSB\Api\Model\Money':
                $v = json_decode($value);
                return ['amount' => intval($v->amount), 'currency' => $v->currency];
            case '\SSB\Api\Model\Validation[]':
                return json_decode($value, true);
        }

        return null;
    }

    protected function makeSeRequest($model)
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
