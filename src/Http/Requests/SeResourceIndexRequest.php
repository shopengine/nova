<?php

namespace Brainspin\Novashopengine\Http\Requests;

use Laravel\Nova\Http\Requests\CountsResources;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Http\Requests\QueriesResources;
use Laravel\Nova\Http\Requests\ResourceIndexRequest;

class SeResourceIndexRequest extends ResourceIndexRequest
{
    use CountsResources, QueriesResources;

    /**
     * Get the paginator instance for the index request.
     *
     * @return array
     */
    public function searchIndex()
    {
        $request = app(NovaRequest::class);

        $paginator = $this->paginator(
            $request, $request->resource()
        );

        return [
            $paginator,
            count($paginator->items())
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
}
