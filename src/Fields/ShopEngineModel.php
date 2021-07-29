<?php

namespace ShopEngine\Nova\Fields;

use Laravel\Nova\Fields\Field;

class ShopEngineModel extends Field
{
    public $component = 'shop-engine-model';

    public function model($shopEngineResource)
    {
        return $this->withMeta(['uriKey' => $shopEngineResource::uriKey()]);
    }

    public function labelFieldName(string $labelFieldName)
    {
        return $this->withMeta(['labelFieldName' => $labelFieldName]);
    }

    public function valueFieldName(string $valueFieldName)
    {
        return $this->withMeta(['valueFieldName' => $valueFieldName]);
    }
}
