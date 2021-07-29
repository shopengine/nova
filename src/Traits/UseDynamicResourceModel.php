<?php

namespace ShopEngine\Nova\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;

trait UseDynamicResourceModel
{
    //
    public static string $model = '#';

    public static function newModel()
    {
        $model = static::getModel();
        return new $model;
    }

    /**
     * Overrides to use dynamic static::getModel()
     *
     * @return bool
     */
    public static function softDeletes()
    {
        // @todo: should we resolve both (static::model / getModel())
        if (isset(static::$softDeletes[static::getModel()])) {
            return static::$softDeletes[static::getModel()];
        }

        return static::$softDeletes[static::getModel()] = in_array(
            SoftDeletes::class, class_uses_recursive(static::newModel())
        );
    }
}
