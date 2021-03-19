<?php namespace Brainspin\Novashopengine\Filter;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class PurchaseStatusFilter extends Filter
{
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

    public function options(Request $request)
    {
        return [
            'Neu' => 'payment_done',
            'Abgebrochen' => 'canceled',
            'Versendet' => 'shipped',
            'JTL Fehler' => 'failed_jtl'
        ];
    }
}
