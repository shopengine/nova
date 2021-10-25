<?php

namespace ShopEngine\Nova\Filter;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;
use SSB\Api\Model\Purchase;

class PurchaseStatusFilter extends Filter
{
    public function name()
    {
        return __('shopengine.filter.purchase_status');
    }

    public function apply(Request $request, $query, $value)
    {
        $query['status-eq'] = $value;

        return $query;
    }

    public function options(Request $request)
    {
        return [
            'Bezahlt'     => Purchase::STATUS_PAYMENT_DONE,
            'Versendet'   => Purchase::STATUS_SHIPPED,
            'Abgebrochen' => Purchase::STATUS_CANCELED,
        ];
    }
}
