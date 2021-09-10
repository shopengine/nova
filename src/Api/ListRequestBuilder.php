<?php
namespace ShopEngine\Nova\Api;


use Laravel\Nova\Http\Requests\NovaRequest;
use ShopEngine\Nova\Resources\Purchase;
use ShopEngine\Nova\Structs\Api\ListRequestStruct;
use ShopEngine\Nova\Structs\Api\RequestFilterStruct;
use Illuminate\Container\Container;
use Illuminate\Pagination\Paginator;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\FilterDecoder;

/**
 * @property array|RequestFilterStruct $filters
 */

class ListRequestBuilder extends RequestBuilder
{

    const PER_PAGE_COUNT = 25;

    public function __construct(
        NovaRequest $request,
        array $filters = []
    )
    {
        parent::__construct($request);
        $this->request = $request;
        $this->filters = $filters;
    }
    /**
     * Paginate the given query into a simple paginator.
     *
     * @param  int|null  $perPage
     * @param  array  $columns
     * @param  string  $pageName
     * @param  int|null  $page
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function simplePaginate(
        $perPage = self::PER_PAGE_COUNT,
        $columns = ['*'],
        $pageName = 'page',
        $page = null
    )
    {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);

        $listRequest = $this->buildFromRequest($perPage);
        return $this->simplePaginator($this->loadItems($listRequest), $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);
    }

    /**
     * Create a new simple paginator instance.
     *
     * @param  \Illuminate\Support\Collection  $items
     * @param  int  $perPage
     * @param  int  $currentPage
     * @param  array  $options
     * @return \Illuminate\Pagination\Paginator
     */
    protected function simplePaginator($items, $perPage, $currentPage, $options) : Paginator
    {
        return Container::getInstance()->makeWith(Paginator::class, compact(
            'items', 'perPage', 'currentPage', 'options'
        ));
    }

    /**
     * Execute the query statement on ShopEngine API.
     *
     * @param \ShopEngine\Nova\Structs\Api\ListRequestStruct $listRequest
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Builder[]
     */
    public function loadItems(ListRequestStruct $listRequest) : array
    {
        $rawResponse = $this->getClient()->get(
            $this->getShopEnginePath(),
            $listRequest->createApiRequest()
        );

        $modelClass = $this->request->resource()::getModel();

        return collect($rawResponse)->map(function ($seModel) use ($modelClass) {
            return new $modelClass($seModel);
        })->all();
    }


    /**
     * Builds Request for ShopEngine
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $novaRequest
     * @param string $perPage
     *
     * @return array
     */
    public function buildFromRequest(
        string $perPage = "25"
    ): ListRequestStruct
    {
        /** @var \ShopEngine\Nova\Resources\ShopEngineResource $resource */
        $resource = $this->request->resource();

        $listRequest = new ListRequestStruct();
        $listRequest->setPageSize($perPage);

        $page = $this->request->get('page') ? $this->request->get('page') - 1 : 0;
        $listRequest->setPage($page);

        $listRequest->setSortDescending($this->request->get('orderByDirection') === 'desc');
        if ($this->request->get('orderBy')) {
            $listRequest->setSort($this->request->get('orderBy'));
        } else {
            $listRequest->setSort($resource::getDefaultSort());
        }

        if ($this->request->has('search')) {
            if ($resource::getFirstSearchKey()) {
                $searchTerm = $this->request->get('search');
                if ($searchTerm) {
                    $listRequest->addFilter(
                        new RequestFilterStruct(
                            $resource::getFirstSearchKey(),
                            urlencode("%{$searchTerm}%"),
                            'like'
                        )
                    );
                }
            }
        }

        foreach ($this->filters as $filter) {
            if ($filter instanceof RequestFilterStruct) {
                $listRequest->addFilter($filter);
            }
        }

        if ($this->request->has('filters')) {
            $filters = (new FilterDecoder(
                $this->request->get('filters'),
                $this->request->newResource()->availableFilters($this->request)
            ))->filters();

            $filterParams = [];
            foreach ($filters as $applyFilterObj) {
                $filterParams = $applyFilterObj->filter->apply($this->request, $filterParams, $applyFilterObj->value);
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

        // @todo: remove this ugly stuff
        if ($resource === Purchase::class) {
            $listRequest->addFilter(new RequestFilterStruct(
                'email',
                'login@brainspin.de',
                'ne'
            ));
        }

        $indexFields = $this->request->newResource()->indexFields($this->request);
        $properties =  $indexFields->map(fn(Field $field) => $field->attribute)
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
    public function count() : int
    {
        $listRequest = $this->buildFromRequest($this->request);

        return $this->getClient()->get(
            $this->getShopEnginePath() . '/count',
            $listRequest->createCountRequest()
        );
    }
}
