<?php

namespace Brainspin\Novashopengine\Filter;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\BooleanFilter;

class CodepoolArchive extends BooleanFilter
{
    public function apply(Request $request, $query, $value)
    {
        if ($value['archive']) {
            $query['archive'] = 'true';
        }

        return $query;
    }

    public function options(Request $request)
    {
        return [
            'Archiviert' => 'archive'
        ];
    }
}
