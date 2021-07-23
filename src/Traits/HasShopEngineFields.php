<?php

namespace Brainspin\Novashopengine\Traits;

use App\Contracts\HttpCacheContract;
use Brainspin\Novashopengine\Events\ShopEngineResourceFieldsLoaded;
use Brainspin\Novashopengine\Events\ShopEngineResourceFieldsStruct;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;

trait HasShopEngineFields
{
    /**
     * @param array $fields
     */
    public function appendShopEngineFields(array $fields) : array {

        $filtered = array_filter(
            event(ShopEngineResourceFieldsLoaded::makeWithStruct(
                $this,
                $fields
            ), fn($el) => is_object($el) && $el instanceof ShopEngineResourceFieldsStruct)
        );

        if (!empty($filtered)) {
            $struct = array_pop($filtered);
            $fields = $struct->fields;
        }

        return $fields;
    }
}
