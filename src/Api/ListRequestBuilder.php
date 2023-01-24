<?php

namespace ShopEngine\Nova\Api;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Laravel\Nova\Http\Requests\NovaRequest;
use ShopEngine\Nova\Models\ShopEngineModel;
use ShopEngine\Nova\Resources\Code;
use ShopEngine\Nova\Resources\Purchase;
use ShopEngine\Nova\Structs\Api\ListRequestStruct;
use ShopEngine\Nova\Structs\Api\RequestFilterStruct;
use Illuminate\Container\Container;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\FilterDecoder;

/**
 * @property array|RequestFilterStruct $filters
 */
class ListRequestBuilder extends RequestBuilder
{
    public function __construct(ShopEngineModel $model, array $filters = [])
    {
        parent::__construct($model);
        $this->filters = $filters;
    }

    /**
     * Paginate the given query into a length-aware paginator.
     *
     * @param int|null $perPage
     * @param array $columns
     * @param string $pageName
     * @param int|null $page
     * @return LengthAwarePaginator
     */
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null): LengthAwarePaginator
    {
        $request = app(NovaRequest::class);

        //$perPage = request()->perPage();
        $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);

        $listRequest = $this->buildFromRequest(
            $request,
            $perPage
        );

        return $this->paginator($this->loadItems($listRequest), $this->count(), $perPage, $page, [
            'path'     => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);
    }

    /**
     * Create a new length-aware paginator instance.
     *
     * @param Collection $items
     * @param int $total
     * @param int $perPage
     * @param int $currentPage
     * @param array $options
     * @return LengthAwarePaginator
     */
    protected function paginator($items, $total, $perPage, $currentPage, $options): LengthAwarePaginator
    {
        return Container::getInstance()->makeWith(LengthAwarePaginator::class, compact(
            'items', 'total', 'perPage', 'currentPage', 'options'
        ));
    }

    /**
     * Execute the query statement on ShopEngine API.
     *
     * @param ListRequestStruct $listRequest
     *
     * @return Collection
     */
    public function loadItems(ListRequestStruct $listRequest): Collection
    {
        $rawResponse = $this->getClient()->get(
            $this->getEndpoint(),
            $listRequest->createApiRequest()
        );

        $modelClass = $this->getModelClass();

        return collect($rawResponse)->map(function ($seModel) use ($modelClass) {
            return new $modelClass($seModel);
        });
    }

    /**
     * Builds Request for ShopEngine
     *
     * @param NovaRequest $request
     * @param string $perPage
     *
     * @return ListRequestStruct
     */
    public function buildFromRequest(NovaRequest $request, string $perPage = '25'): ListRequestStruct
    {
        /** @var \ShopEngine\Nova\Resources\ShopEngineResource $resource */
        $resource = $request->resource();

        $listRequest = new ListRequestStruct();

        if ($request->viaResourceId) {
            if (
                $request->viaRelationship
                && $request->viaResource()
            ) {
                $resourceModel = $request->newViaResource()::newModel();

                // monkey-potching to prevent additional api call - needs testing
                $resourceModel->offsetSet($resourceModel->getKeyName(), $request->viaResourceId);
                $listRequest = $resourceModel->{$request->viaRelationship}();
            } else {
                $listRequest->addFilter(new RequestFilterStruct(
                    'codepoolId',
                    $request->viaResourceId,
                    'eq'
                ));
            }
        }

        $listRequest->setPageSize($perPage);

        $page = $request->get('page') ? $request->get('page') - 1 : 0;
        $listRequest->setPage($page);

        $listRequest->setSortDescending($request->get('orderByDirection') === 'desc');
        if ($request->get('orderBy')) {
            $listRequest->setSort($request->get('orderBy'));
        } else {
            $listRequest->setSort($resource::getDefaultSort());
        }

        if ($request->has('search')) {
            if ($resource::getFirstSearchKey()) {
                $searchTerm = $request->get('search');
                if ($searchTerm) {
                    $searchTerm .= '%';
                    if (!in_array($resource, [Code::class, Purchase::class])) {
                        $searchTerm = '%' . $searchTerm;
                    }
                    $listRequest->addFilter(
                        new RequestFilterStruct(
                            $resource::getFirstSearchKey(),
                            urlencode($searchTerm),
                            'like'
                        )
                    );
                }
            }
        }

        if ($request->has('id-eq')) {
            $listRequest->addFilter(new RequestFilterStruct(
                'id',
                $request->get('id-eq'),
                'eq'
            ));
        }

        foreach ($this->filters as $filter) {
            if ($filter instanceof RequestFilterStruct) {
                $listRequest->addFilter($filter);
            }
        }

        if ($request->has('filters')) {
            $filters = (new FilterDecoder(
                $request->get('filters'),
                $request->newResource()->availableFilters($request)
            ))->filters();

            $filterParams = [];
            foreach ($filters as $applyFilterObj) {
                $filterParams = $applyFilterObj->filter->apply($request, $filterParams, $applyFilterObj->value);
            }

            foreach ($filterParams as $field => $value) {
                $listRequest->addFilter(
                    new RequestFilterStruct(
                        $field,
                        $value
                    )
                );
            }
        }

        // @todo Solve in shopengine core
        if ($resource === Purchase::class) {
            $listRequest->addFilter(new RequestFilterStruct(
                'email',
                'login@brainspin.de',
                'ne'
            ));
        }

        $indexFields = $request->newResource()->indexFields($request);
        $properties  = $indexFields->map(fn(Field $field) => $field->attribute)
            ->add($resource::$id)
            ->toArray();
        $listRequest->setProperties($properties);

        return $listRequest;
    }

    /**
     * Response for Nova /count api call
     *
     * @return int
     */
    public function count(): int
    {
        $request     = app(NovaRequest::class);
        $listRequest = $this->buildFromRequest($request);

        return $this->getClient()->get(
            $this->getEndpoint() . '/count',
            $listRequest->createCountRequest()
        );
    }
}
