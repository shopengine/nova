<?php

namespace ShopEngine\Nova\Api;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Laravel\Nova\Http\Requests\NovaRequest;
use ShopEngine\Nova\Services\ConfiguredClassFactory;
use SSB\Api\Client;

abstract class RequestBuilder
{

    /**
     * @var \Laravel\Nova\Http\Requests\NovaRequest
     */
    protected NovaRequest $request;

    public function __construct(NovaRequest $request)
    {
        $this->request = $request;
    }

    /**
     *  Get Endpoint of Resource
     *
     * @return string
     */
    protected function getShopEnginePath(): string
    {
        return $this->request->resource()::getShopEngineEndpoint();
    }

    /**
     *  Get Api Client
     *
     * @return \SSB\Api\Client
     * @throws \Exception
     */
    protected function getClient(): Client
    {
        $shopService = ConfiguredClassFactory::getShopEngineService();
        return $shopService->shopEngineClient($shopService->shopEngineSettings());
    }

    /**
     *  Fakes a Base cause have no base
     */
    public function toBase()
    {
        return $this;
    }

    /**
     * @param $value
     * @param string $type
     * @return array|bool|int|mixed|string|null
     */
    protected function fixTypes($value, string $type)
    {
        if ($value === null) {
            return null;
        }

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
            case '\DateTime':
                if (empty($value)) {
                    return null;
                }
                return Carbon::parse($value)->format('Y-m-d H:i:s');
            default:
                Log::info('Type not supported ' . $type);
                break;
        }

        return null;
    }
}
