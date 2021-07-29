<?php

namespace ShopEngine\Nova\Contracts;

use SSB\Api\Client;
use SSB\Api\Contracts\ShopEngineSettingsInterface;

interface NovaShopEngineInterface {

    public function shopCurrency() : string;
    public function shopEngineSettings(string $shopIdentifier = null) : ShopEngineSettingsInterface;
    public function shopEngineClient(ShopEngineSettingsInterface $settings = null): Client;
    public function shopRegion() : string;

}
