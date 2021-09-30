<?php

namespace ShopEngine\Nova\Api;

use ShopEngine\Nova\Models\ShopEngineModel;
use ShopEngine\Nova\Services\ConfiguredClassFactory;
use SSB\Api\Client;

abstract class RequestBuilder
{
    protected ShopEngineModel $model;

    public function __construct(ShopEngineModel $model)
    {
        $this->model = $model;
    }

    protected function getModelClass(): string
    {
        return get_class($this->model);
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        $modelClass = $this->getModelClass();
        return $modelClass::$apiEndpoint;
    }

    public static function fromResource(string $resource)
    {
        $modelClass = $resource::getModel();
        return new static(new $modelClass());
    }

    public static function fromClass(string $modelClass)
    {
        return new static(new $modelClass());
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

    public function when($value, $callback, $default = null)
    {
        if ($value) {
            return $callback($this, $value) ?: $this;
        } elseif ($default) {
            return $default($this, $value) ?: $this;
        }

        return $this;
    }
}
