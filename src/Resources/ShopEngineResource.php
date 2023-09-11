<?php

namespace ShopEngine\Nova\Resources;

use ShopEngine\Nova\Api\ListRequestBuilder;
use ShopEngine\Nova\Contracts\ShopEngineResourceInterface;
use ShopEngine\Nova\Models\ShopEngineModel;
use ShopEngine\Nova\Structs\Api\RequestFilterStruct;
use ShopEngine\Nova\Traits\HasShopEngineFields;
use ShopEngine\Nova\Traits\UseDynamicResourceModel;
use Illuminate\Http\Request;
use Laravel\Nova\Authorizable;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;
use Laravel\Nova\TrashedStatus;

abstract class ShopEngineResource extends Resource implements ShopEngineResourceInterface
{
    use UseDynamicResourceModel;
    use Authorizable;
    use HasShopEngineFields;

    public static $search = [];
    public static $defaultSort = 'id';

    public static $globallySearchable = false;
    public static $displayInNavigation = false;
    public static $canImportResource = false;

    public static $perPageOptions = [100];

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
     * Injecting seModel to js environment.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function serializeForDetail(NovaRequest $request, Resource $resource = null)
    {
        $serialized = parent::serializeForDetail($request, $resource);
        $seModel = null;
        if (!empty($serialized['fields'])) {
            $seModel = (new ShopEngineModel($serialized['fields'][0]->resource->model))->jsonSerialize();
        }
        return array_merge($serialized, [
            'seModel' => $seModel
        ]);
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
     * @return \ShopEngine\Nova\Api\ListRequestBuilder
     */

    public static function buildIndexQuery(
        NovaRequest $request,
        $query = null,
        $search = null,
        array $filters = [],
        array $orderings = [],
        $withTrashed = TrashedStatus::DEFAULT)
    {

        if ($request->has('id-eq')) {
            $filters[] =new RequestFilterStruct(
                'id',
                $request->get('id-eq'),
                'eq'
            );
        }

        return new ListRequestBuilder($request, $filters);
    }
}
