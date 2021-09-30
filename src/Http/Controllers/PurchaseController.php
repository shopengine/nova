<?php

namespace ShopEngine\Nova\Http\Controllers;

class PurchaseController extends ShopEngineNovaController
{
    public function manualJTL(string $resourceId)
    {
        $data = [
            'originStatus' => \SSB\Api\Model\Purchase::ORIGIN_STATUS_READY_TO_IMPORT
        ];

        try {
            $this->getClient()->patch("purchase/$resourceId", $data);
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage());
        }

        return response()->json('ok');
    }
}
