<?php

namespace ShopEngine\Nova\Http\Controllers;

use Laravel\Nova\Http\Requests\NovaRequest;

class CodelessController extends ShopEngineNovaController
{
    public function index(NovaRequest $request)
    {
        return $this->getClient()->get('codeless', [], 'true');
    }
}
