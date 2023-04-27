<?php

namespace ShopEngine\Nova\Http\Controllers\MarketingProvider;

use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\NovaRequest;
use ShopEngine\Nova\Http\Controllers\ShopEngineNovaController;

class KlicktippPeriodTagController extends ShopEngineNovaController
{
    protected string $apiPath = 'settings/marketing-provider/klicktipp/period-tags';

    public function index(NovaRequest $request)
    {
        return $this->getClient()->get($this->apiPath);
    }

    public function show(string $tag)
    {
        return $this->getClient()->get("{$this->apiPath}/{$tag}");
    }

    public function store(Request $request)
    {
        return $this->getClient()->post($this->apiPath, $request->periodTag);
    }

    public function update(Request $request)
    {
        return $this->getClient()->patch($this->apiPath, $request->periodTag);
    }

    public function destroy(string $tag)
    {
        return $this->getClient()->post("{$this->apiPath}/{$tag}/delete", []);
    }
}
