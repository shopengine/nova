<?php

namespace ShopEngine\Nova\Filter;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class PurchaseStatusFilter extends Filter
{
    public function name()
    {
        return __('shopengine.filter.purchase_status');
    }

    public function apply(Request $request, $query, $value)
    {
        if ($value === 'failed_jtl') {
            $query['originStatus-eq'] = 'imported';
            $query['originId-eq'] = 'empty';
            $query['orderDate-gt'] = '2020-05-25';
        }
        else {
            $query['status-eq'] = $value;
        }

        return $query;
    }

    // @todo: this types should be defined in ssb
    public function options(Request $request)
    {
        return [
            'Neu'         => 'payment_done',
            'Versendet'   => 'shipped',
            'Abgebrochen' => 'canceled',
            'JTL Fehler'  => 'failed_jtl'
        ];
    }
}
