<?php

namespace ShopEngine\Nova\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\NovaRequest;

class CodelessController extends ShopEngineNovaController
{
    public function toggleStatus(Request $request)
    {
        return $this->getClient()->post('codeless/toggle-status', $request->codeless);
    }
}