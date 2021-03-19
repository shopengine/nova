<?php

namespace Brainspin\Novashopengine\Http\Controllers;

use App\Services\ShopService;
use Illuminate\Routing\Controller;
use SSB\Api\Contracts\ShopEngineSettingsInterface;
use SSB\Api\LaravelClient;
use SSB\Api\Model\Code;

class ShopEngineNovaController extends Controller
{
    /** @var ShopService */
    protected $shop;

    public function __construct()
    {
        $this->shop = app()->make('shop');
    }

    protected function getShopSettings(): ShopEngineSettingsInterface
    {
        return $this->shop->settings();
    }

    protected function getClient(): LaravelClient
    {
        $clientFactory = app()->make('ShopEngineApiClient');

        return $clientFactory->make($this->getShopSettings());
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
