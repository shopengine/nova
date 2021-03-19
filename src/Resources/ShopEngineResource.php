<?php namespace Brainspin\Novashopengine\Resources;

use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;

abstract class ShopEngineResource extends Resource
{
    public static $globallySearchable = false;
    public static $displayInNavigation = false;
    public static $canImportResource = false;

    public function getKey()
    {
        return $this->model[$this::$id];
    }

    public function authorizedToDelete(Request $request)
    {
        return false;
    }

    public static function redirectAfterCreate(NovaRequest $request, $resource)
    {
        $uriKey = static::uriKey();
        $resourceKey = $resource->getKey();

        return "/novashopengine/$uriKey/$resourceKey";
    }

    public static function redirectAfterUpdate(NovaRequest $request, $resource)
    {
        $uriKey = static::uriKey();
        $resourceKey = $resource->getKey();

        return "/novashopengine/$uriKey/$resourceKey";
    }
}
