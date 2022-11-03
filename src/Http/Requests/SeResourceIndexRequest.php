<?php

namespace ShopEngine\Nova\Http\Requests;

use Laravel\Nova\Contracts\QueryBuilder;
use Laravel\Nova\Http\Requests\CountsResources;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Http\Requests\QueriesResources;
use Laravel\Nova\Http\Requests\ResourceIndexRequest;

class SeResourceIndexRequest extends ResourceIndexRequest
{
    use CountsResources, QueriesResources;

    const PER_PAGE_COUNT = 200;

    /**
     * Get the paginator instance for the index request.
     *
     * @return array
     */
    public function searchIndex()
    {

        // @todo may use custom querybuilder for se
        $request = app(NovaRequest::class);

        $paginator = $this->paginator(
            $request, $request->resource()
        );

        return [
            $paginator,
            count($paginator->items()),
            false
        ];
    }

    protected function paginator(ResourceIndexRequest $request, $resource)
    {
        return $request->toQuery()->simplePaginate(
            $request->viaRelationship()
                ? $resource::$perPageViaRelationship
                : ($request->perPage ?? $resource::perPageOptions()[0])
        );
    }


    public function perPage()
    {
        if ($this->request->has('perPage')) {
            return $this->request->get('perPage');
        }

        return static::PER_PAGE_COUNT;
    }
}
