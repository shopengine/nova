<?php

namespace Brainspin\Novashopengine\Events;

use Brainspin\Novashopengine\Models\ShopEngineModel;
use Brainspin\Novashopengine\Resources\ShopEngineResource;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Laravel\Nova\Resource;

class ShopEngineResourceFieldsLoaded
{
    use Dispatchable, SerializesModels;

    public ShopEngineResourceFieldsStruct $data;

    /**
     * ShopEngineResourceFieldsLoaded constructor.
     *
     * @param \Brainspin\Novashopengine\Events\ShopEngineResourceFieldsStruct $data
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
