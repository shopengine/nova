<?php

namespace ShopEngine\Nova\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Laravel\Nova\Resource;

class ShopEngineResourceFieldsLoaded
{
    use Dispatchable, SerializesModels;

    public ShopEngineResourceFieldsStruct $data;

    /**
     * ShopEngineResourceFieldsLoaded constructor.
     *
     * @param \ShopEngine\Nova\Events\ShopEngineResourceFieldsStruct $data
     */
    public function __construct(ShopEngineResourceFieldsStruct $data)
    {
        $this->data = $data;
    }

    public static function makeWithStruct(Resource $resource, array $fields)
    {
        return new static(
            new ShopEngineResourceFieldsStruct($resource, $fields)
        );
    }
}
