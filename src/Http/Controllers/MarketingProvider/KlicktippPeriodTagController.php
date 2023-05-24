<?php

namespace ShopEngine\Nova\Http\Controllers\MarketingProvider;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use ShopEngine\Nova\Http\Controllers\ShopEngineNovaController;

class KlicktippPeriodTagController extends ShopEngineNovaController
{
    protected string $apiPath = 'settings/marketing-provider/klicktipp/period-tags';

    private function getOptions(): array
    {
        if (config('klicktipp.username') &&
            config('klicktipp.developer_key') &&
            config('klicktipp.customer_key') &&
            config('klicktipp.service')
        ) {
            $options = Cache::remember('klicktipp.tag.options', 60, function () {
                return App::make('klicktipp')->tag_index();
            });
        } else {
            $options = [];
        }

        asort($options);

        return $options;
    }

    public function options(): JsonResponse
    {
        return response()->json($this->getOptions(), 200);
    }

    public function index()
    {
        $response = $this->getClient()->get($this->apiPath);

        $options = $this->getOptions();

        foreach ($response as &$tag) {
            $tag->id = $tag->tag;
            $tag->name = $options[$tag->tag] ?? '';
        }

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

        $options = $this->getOptions();

        $response['id'] = $response['tag'];
        $response['name'] = $options[$response['tag']] ?? '';

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
