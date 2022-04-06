<?php

namespace ShopEngine\Nova\Filter;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;
use SSB\Api\Model\Purchase;

class PurchaseOriginStatusFilter extends Filter
{
    public function name()
    {
        return __('shopengine.filter.purchase_origin_status');
    }

    public function apply(Request $request, $query, $value)
    {
        $query['originStatus-eq'] = $value;

        return $query;
    }

    public function options(Request $request)
    {
        return [
            'Importierbar' => Purchase::ORIGIN_STATUS_READY_TO_IMPORT,
            'Importiert'   => Purchase::ORIGIN_STATUS_IMPORTED,
            'Fehler'       => Purchase::ORIGIN_STATUS_ERROR_IN_IMPORT,
            'Angehalten'   => Purchase::ORIGIN_STATUS_WAIT_FOR_MANUAL,
        ];
    }
}
