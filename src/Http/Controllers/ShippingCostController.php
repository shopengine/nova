<?php

namespace ShopEngine\Nova\Http\Controllers;

use Laravel\Nova\Http\Requests\NovaRequest;

class ShippingCostController extends ShopEngineNovaController
{
    public function addOption(string $resource, string $resourceId, NovaRequest $request)
    {
        if ($resource !== 'shipping-costs') {
            abort(404, 'Not Supported');
        }

        $values = $request->get('values');

        abort_if($values === null, 400, 'No Values');
        abort_if(!isset($values['price']), 400, 'No Price');
        abort_if(!isset($values['validation']), 400, 'No Validation');

        $response = $this->getClient()->post("shippingcost/event/$resourceId", [
            'eventType' => 'addOption',
            'values' => $values
        ]);

        // @todo return new data

        return response()->json([
            'msg' => 'ok'
        ]);
    }

    public function removeOption(string $resource, string $resourceId, NovaRequest $request)
    {
        if ($resource !== 'shipping-costs') {
            abort(404, 'Not Supported');
        }

        $values = $request->get('values');
        abort_if($values === null, 400, 'No Values');
        abort_if(!isset($values['optionId']), 400, 'No Option Id');

        $response = $this->getClient()->post("shippingcost/event/$resourceId", [
            'eventType' => 'removeOption',
            'values' => [
                'shippingCostOptionId' => $values['optionId'],
            ],
        ]);

        return response()->json([
            'msg' => 'ok'
        ]);
    }
}
