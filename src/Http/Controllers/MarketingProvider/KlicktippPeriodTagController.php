<?php

namespace ShopEngine\Nova\Http\Controllers\MarketingProvider;

use Exception;
use Illuminate\Http\Request;
use ShopEngine\Nova\Http\Controllers\ShopEngineNovaController;

class KlicktippPeriodTagController extends ShopEngineNovaController
{
    protected string $apiPath = 'settings/marketing-provider/klicktipp/period-tags';

    public function index()
    {
        $response = $this->getClient()->get($this->apiPath);

        return $response;
    }

    /**
     * @throws Exception
     */
    public function show(string $tag): array
    {
        $response = $this->getClient()->get("{$this->apiPath}/{$tag}");

        $response = (array)$response;

        if (empty($response)) {
            throw new Exception('Tag not found');
        }

        return $response;
    }

    /**
     * @throws Exception
     */
    public function store(Request $request): array
    {
        $response = $this->getClient()->post($this->apiPath, $request->periodTag);

        $response = (array)$response;

        if (empty($response)) {
            throw new Exception('Tag not stored');
        }

        return $response;
    }

    /**
     * @throws Exception
     */
    public function update(Request $request): array
    {
        $response = $this->getClient()->patch($this->apiPath, $request->periodTag);

        $response = (array)$response;

        if (empty($response)) {
            throw new Exception('Tag not found');
        }

        return $response;
    }

    public function destroy(string $tag): array
    {
        $response = $this->getClient()->delete("{$this->apiPath}", ['tag' => $tag]);

        $response = (array)$response;

        return $response;
    }
}
