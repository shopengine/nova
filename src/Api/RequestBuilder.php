<?php
namespace ShopEngine\Nova\Api;

use ShopEngine\Nova\Services\ConfiguredClassFactory;
use Laravel\Nova\Http\Requests\NovaRequest;
use SSB\Api\Client;

abstract class RequestBuilder {

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
    protected function getShopEnginePath() : string {
        return $this->request->resource()::getShopEngineEndpoint();
    }

    /**
     *  Get Api Client
     *
     * @return \SSB\Api\Client
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
     * A really ugly function
     * @todo fix me please
     *
     * @param $value
     * @param string $type
     *
     * @return array|bool|int|mixed|string|null
     */
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
}
