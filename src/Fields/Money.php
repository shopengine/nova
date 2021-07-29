<?php

namespace ShopEngine\Nova\Fields;

use Laravel\Nova\Fields\Field;

class Money extends Field
{
    public $component = 'money';

    public function currency(string $currency)
    {
        return $this->withMeta(['currency' => $currency]);
    }

    public function locale(string $locale)
    {
        return $this->withMeta(['locale' => $locale]);
    }
}
