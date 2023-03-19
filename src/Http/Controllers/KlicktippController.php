<?php

namespace ShopEngine\Nova\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;

class KlicktippController extends ShopEngineNovaController
{

    public function index(NovaRequest $request)
    {
       return $this->getClient()->get('klicktipp');
    }

    public function show($klicktipp)
    {
        return $this->getClient()->get('klicktipp', ['klicktipp' => $klicktipp]);
    }

    public function update(Request $request)
    {
        $data = [
            'klicktipp' => $request->value
        ];
        return $this->getClient()->patch('klicktipp', $data);
    }


    public function store(Request $request)
    {
        $data = [
            'klicktipp' => $request->value
        ];
        return $this->getClient()->post('klicktipp', $data);
    }

    public function destroy(string $tag)
    {
        return $this->getClient()->get("klicktipp/$tag/delete");
    }
}
