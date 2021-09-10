<?php

namespace ShopEngine\Nova\Contracts;

use ShopEngine\Nova\Structs\Navigation\NavigationStruct;

interface ShopEnginePackageInterface {

    public static function getShopengineNavigation() : ?NavigationStruct;
    public static function getLanguagePath() : ?string;

}
