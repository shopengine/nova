<?php

namespace ShopEngine\Nova\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Laravel\Nova\Http\Requests\NovaRequest;

class CodelessController extends ShopEngineNovaController
{
    public function index(NovaRequest $request)
    {
        return $this->getClient()->get('codeless', [], 'true');
    }


    public function show(string $id)
    {
        return $this->getClient()->get('codeless/' . $id , [], 'true');
    }

    public function update(Request $request): JsonResponse
    {
        return response()->json($request);
        return $this->getClient()->patch('codeless', $request->aggregateId);

    }
}
