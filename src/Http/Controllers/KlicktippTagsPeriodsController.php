<?php

namespace ShopEngine\Nova\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;

class KlicktippTagsPeriodsController extends ShopEngineNovaController
{

    public function index(NovaRequest $request)
    {

       return $this->getClient()->get('settings/newsletter-provider/klicktipp/tags-periods');
    }

    public function show(int $tag)
    {
        return $this->getClient()->get("settings/newsletter-provider/klicktipp/tags-periods/{$tag}");
    }

    public function update(Request $request)
    {
        $data = [
            'tagPeriod' => $request->tagPeriod
        ];
        return $this->getClient()->patch('settings/newsletter-provider/klicktipp/tags-periods', $data);
    }


    public function store(Request $request)
    {
        $data = [
            'tagPeriod' => $request->tagPeriod
        ];
        return $this->getClient()->post('settings/newsletter-provider/klicktipp/tags-periods', $data);
    }

    public function destroy(int $tag)
    {
        return $this->getClient()->post("settings/newsletter-provider/klicktipp/tags-periods/{$tag}/delete", []);
    }
}
