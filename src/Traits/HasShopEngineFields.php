<?php

namespace ShopEngine\Nova\Traits;

use ShopEngine\Nova\Events\ShopEngineResourceFieldsLoaded;
use ShopEngine\Nova\Events\ShopEngineResourceFieldsStruct;

trait HasShopEngineFields
{
    /**
     * @param array $fields
     */
    public function appendShopEngineFields(array $fields): array
    {
        $filtered = array_filter(
            event(ShopEngineResourceFieldsLoaded::makeWithStruct(
                $this,
                $fields
            ), fn ($el) => is_object($el) && $el instanceof ShopEngineResourceFieldsStruct)
        );

        if (!empty($filtered)) {
            $struct = array_pop($filtered);
            $fields = $struct->fields;
        }

        return $fields;
    }
}
