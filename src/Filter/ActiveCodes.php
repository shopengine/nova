<?php

namespace ShopEngine\Nova\Filter;

use DateTime;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\BooleanFilter;

class ActiveCodes extends BooleanFilter
{
    public function apply(Request $request, $query, $value)
    {
        if ($value['active_codes']) {
            $query['status-eq'] = 'enabled';
            $query['usageCount-null'] = 'null';
            $query['start-nlt'] = (new DateTime())->format('Y-m-d H:i:s');
            $query['expires-ngt'] = (new DateTime())->format('Y-m-d H:i:s');
            $query['conditionSetVersionId-ne'] = '204|133';
            $query['codepoolId-ne'] = '124|166';
        }

        return $query;
    }

    public function options(Request $request)
    {
        return [
            'Aktive Codes' => 'active_codes'
        ];
    }
}
