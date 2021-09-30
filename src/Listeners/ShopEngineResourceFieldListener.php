<?php

namespace ShopEngine\Nova\Listeners;

use ShopEngine\Nova\Events\ShopEngineResourceFieldsStruct;
use ShopEngine\Nova\Models\ShopEngineModel;

abstract class ShopEngineResourceFieldListener
{
    /**
     * Handle user login events.
     */
    public function handle($event): ShopEngineResourceFieldsStruct
    {

        /** @var ShopEngineResourceFieldsStruct $fieldStruct */
        $fieldStruct = $event->data;

        if (get_class($fieldStruct->resource) !== static::resourceClass()) {
            return $fieldStruct;
        }

        return $fieldStruct->appendFields(
            $this->customFields($fieldStruct->model)
        );
    }

    public static function resourceClass(): string
    {
        return 'null';
    }

    public function customFields(ShopEngineModel $model = null): array
    {
        return [];
    }
}
