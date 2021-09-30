<?php

namespace ShopEngine\Nova\Services;

use SSB\Api\Client;
use SSB\Api\ClientFactory;
use SSB\Api\Contracts\ShopEngineSettingsInterface;

// @todo add defaults
class ShopEngineNovaService
{
    public function __construct()
    {
    }

    public function getShopEngineSettings()
    {
    }

    /**
     * Get the current shop settings.
     *
     * @return mixed
     * @throws Exception
     */
    public function settings(): ShopEngineSettingsInterface
    {
        return $this->current()->settings;
    }

    /**
     * @var \SSB\Api\ClientFactory
     */
    private ClientFactory $clientFactory;

    public function shopEngineClient(ShopEngineSettingsInterface $settings): Client
    {
        return $this->clientFactory->make($settings);
    }
}
