<?php
namespace Brainspin\Novashopengine\Services;

use App\Models\ShopSetting;

use SSB\Api\Client;
use SSB\Api\ClientFactory;
use SSB\Api\Contracts\ShopEngineSettingsInterface;

class ShopEngineNovaService {

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
    public function settings(): ShopSetting
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


