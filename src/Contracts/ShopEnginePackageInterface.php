<?php

namespace Brainspin\Novashopengine\Contracts;

use Brainspin\Novashopengine\Structs\Navigation\NavigationStruct;

interface ShopEnginePackageInterface {
    public static function getShopengineNavigation() : ?NavigationStruct;
}
