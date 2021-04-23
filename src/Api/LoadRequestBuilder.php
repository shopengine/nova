<?php
namespace Brainspin\Novashopengine\Api;


use Brainspin\Novashopengine\Models\ShopEngineModel;
use Brainspin\Novashopengine\Resources\Purchase;
use Brainspin\Novashopengine\Services\ConfiguredClassFactory;
use Illuminate\Container\Container;
use Illuminate\Pagination\Paginator;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\FilterDecoder;
use Laravel\Nova\Http\Requests\NovaRequest;
use SSB\Api\Client;

class LoadRequestBuilder extends RequestBuilder
{

    const PER_PAGE_COUNT = 25;

    /**
     * Paginate the given query into a simple paginator.
     *
     * @param  int|null  $perPage
     * @param  array  $columns
     * @param  string  $pageName
     * @param  int|null  $page
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function simplePaginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);
        $perPage = $perPage ?: PER_PAGE_COUNT;

        return $this->simplePaginator($this->loadItems($this->request,$perPage), $perPage, $page, [
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
     * @todo Refactor me please (its old stuff)
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Builder[]
     */
    private function loadItems(NovaRequest $novaRequest, int $perPage) {
        $request = $this->buildShopEngineRequest($novaRequest, $perPage);
        $rawResponse = $this->getClient()->get($this->getShopEnginePath(), $request);

        return collect($rawResponse)->map(function ($seModel) {
            return new ShopEngineModel($seModel);
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
    private function buildShopEngineRequest(
        NovaRequest $novaRequest,
        string $perPage = "25"
    ): array {

        /** @var \Brainspin\Novashopengine\Resources\ShopEngineResource $resource */
        $resource = $novaRequest->resource();

        $seRequest = [
            'pageSize' =>  $perPage,
            'page' => $novaRequest->get('page') ? $novaRequest->get('page') - 1 : 0
        ];

        $sortPrefix = $novaRequest->get('orderByDirection') === 'asc' ? '-' : '';
        if ($novaRequest->get('orderBy')) {
            $seRequest['sort'] = $sortPrefix . $novaRequest->get('orderBy');
        } else {
            $seRequest['sort'] = $sortPrefix . $resource::getDefaultSort();
        }

        if ($novaRequest->has('search')) {
            if ($resource::getFirstSearchKey()) {
                $searchTerm = $novaRequest->get('search');
                if ($searchTerm) {
                    $searchKey = "{$resource::getFirstSearchKey()}-like";
                    $seRequest[$searchKey] = urlencode("%{$searchTerm}%");
                }
            }
        }

        if ($novaRequest->has('filters')) {

            $filters = (new FilterDecoder(
                $novaRequest->get('filters'),
                $this->request->newResource()->availableFilters($this->request)
            ))->filters();

            $filterParams = [];
            foreach ($filters as $applyFilterObj) {
                $filterParams = $applyFilterObj->filter->apply($this->request, $filterParams, $applyFilterObj->value);
            }

            $seRequest = $seRequest + $filterParams;
        }

        // @todo: remove this ugly stuff
        if ($resource === Purchase::class) {
            // todo: remove fixed strings
            $seRequest['email-ne'] = 'login@brainspin.de';
        }

        $indexFields = $this->request->newResource()->indexFields($this->request);
        $seRequest['properties'] = $indexFields->map(fn(Field $field) => $field->attribute)
            ->add($resource::$id)
            ->join('|');

        return $seRequest;
    }

    /**
     * Response for Nova /count api call
     *
     * @return int
     */
    public function count() : int
    {
        $seRequest = $this->buildShopEngineRequest($this->request);

        // @todo shopengine but - if page is large > 0
        unset($seRequest['sort']);
        unset($seRequest['page']);
        unset($seRequest['pageSize']);

        return $this->getClient()->get(
            $this->getShopEnginePath() . '/count',
            $seRequest
        );
    }
}
