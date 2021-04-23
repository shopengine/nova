<?php

namespace Brainspin\Novashopengine\Contracts;

use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\NovaRequest;

interface ShopEngineResourceInterface
{
    public function getKey() : string;

    public function authorizedToDelete(Request $request);

    public static function getFirstSearchKey() : ?string;

    public static function getDefaultSort() : string;

    public static function getShopEngineEndpoint() : string;

    public static function getModel() : string;

    public static function buildIndexQuery(
        NovaRequest $request,
        string $query,
        string $search,
        array $filters,
        array $orderings,
        string $withTrashed
    );
}
