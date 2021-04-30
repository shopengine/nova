<?php namespace Brainspin\Novashopengine\Resources;

use Brainspin\Novashopengine\Api\ListRequestBuilder;
use Brainspin\Novashopengine\Contracts\ShopEngineResourceInterface;
use Brainspin\Novashopengine\Traits\UseDynamicResourceModel;
use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;
use Laravel\Nova\TrashedStatus;

abstract class ShopEngineResource extends Resource implements ShopEngineResourceInterface
{
    use UseDynamicResourceModel;

    public static $search = [];
    public static $defaultSort = 'id';

    public static $globallySearchable = false;
    public static $displayInNavigation = false;
    public static $canImportResource = false;

    public function getKey() : string
    {
        return $this->model[$this::$id] || '';
    }

    public static function getFirstSearchKey() : ?string {
        return static::$search[0] ?? null;
    }

    public static function getDefaultSort() : string {
        return static::$defaultSort;
    }

    public function authorizedToDelete(Request $request)
    {
        return false;
    }

    public static function redirectAfterUpdate(NovaRequest $request, $resource)
    {
        $uriKey = static::uriKey();
        $resourceKey = $request->resourceId;

        return "/resources/$uriKey/$resourceKey";
    }

    /**
     *
     * Replaces the Eloquent Builder with SE Api Client Builder
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param null $query
     * @param null $search
     * @param array $filters
     * @param array $orderings
     * @param string $withTrashed
     *
     * @return \Brainspin\Novashopengine\Api\ListRequestBuilder
     */

    public static function buildIndexQuery(
        NovaRequest $request,
        $query = null,
        $search = null,
        array $filters = [],
        array $orderings = [],
        $withTrashed = TrashedStatus::DEFAULT)
    {
        return new ListRequestBuilder($request);
    }
}
