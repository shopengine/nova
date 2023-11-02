<?php

namespace ShopEngine\Nova\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\NovaRequest;

class CodeController extends ShopEngineNovaController
{
    public function toggleStatus(Request $request)
    {
        return $this->getClient()->post('code/toggle-status', $request->aggregateId);
    }
}
