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

class RequestBuilder
{

    const PER_PAGE_COUNT = 25;

    /**
     * @var \Laravel\Nova\Http\Requests\NovaRequest
     */
    private NovaRequest $request;

    public function __construct(NovaRequest $request)
    {
        $this->request = $request;
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
    public function simplePaginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);

        $perPage = $perPage ?: PER_PAGE_COUNT;

        //$this->skip(($page - 1) * $perPage)->take($perPage + 1);

        return $this->simplePaginator($this->loadItems(), $perPage, $page, [
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
    private function loadItems() {
        $resource = $this->request->resource();

        $seRequest = $this->request->all();

        $sort = false;
        $sortDesc = false;

        if (isset($seRequest["{$resource::uriKey()}_order"])) {
            $sort = $seRequest["{$resource::uriKey()}_order"];
            unset($seRequest["{$resource::uriKey()}_order"]);
        }

        if (isset($seRequest["{$resource::uriKey()}_direction"])) {
            $sortDesc = $seRequest["{$resource::uriKey()}_direction"] === 'desc';
            unset($seRequest["{$resource::uriKey()}_direction"]);
        }

        if (isset($seRequest['search'])) {
            $searchAttribute = $resource::$search[0] ?? false;
            if ($searchAttribute) {
                $seRequest["{$searchAttribute}-like"] = urlencode("%{$seRequest['search']}%");
            }
            unset($seRequest['search']);
        }

        if (isset($seRequest["{$resource::uriKey()}_filter"])) {
            $filter = $seRequest["{$resource::uriKey()}_filter"];
            unset($seRequest["{$resource::uriKey()}_filter"]);

            $filters = (new FilterDecoder($filter, $this->request->newResource()->availableFilters($this->request)))->filters();
            foreach ($filters as $applyFilterObj) {
                $seRequest = $applyFilterObj->filter->apply($this->request, $seRequest, $applyFilterObj->value);
            }
        }

        $seRequestCount = $seRequest;
        unset($seRequestCount['page']);
        unset($seRequestCount['pageSize']);

        if ($sort) {
            $seRequest['sort'] = ($sortDesc ? '-' : '') . $sort;
        }
        else {
            $seRequest['sort'] = $resource::$defaultSort;
        }

        if ($resource === Purchase::class) {
            // todo: remove fixed strings
            $seRequest['email-ne'] = 'login@brainspin.de';
        }

        $indexFields = $this->request->newResource()->indexFields($this->request);

        $seRequest['properties'] = $indexFields->map(fn(Field $field) => $field->attribute)
            ->add($resource::$id)
            ->join('|');

        // todo: add params
        //dd($seRequestCount);
        //$count = $client->get($shopEnginePath . '/count', [] $seRequestCount); //);
        $rawResponse = $this->getClient()->get($this->getShopEnginePath(), []);//$seRequest);

        return collect($rawResponse)->map(function ($seModel) {
            return new ShopEngineModel($seModel);
        })->all();
    }

    private function buildRequest() {
        // @todo: build me
    }


    /**
     *  Fakes a Base cause have no base
     */
    public function toBase()
    {
        return $this;
    }

    public function count() : int
    {
        return $this->getClient()->get($this->getShopEnginePath() . '/count', []);
    }

    private function getShopEnginePath() : string {
        return $this->request->resource()::$shopEnginePath;
    }

    /**
     *  Get Api Client
     *
     * @return \SSB\Api\Client
     */
    private function getClient(): Client
    {
        $shopService = ConfiguredClassFactory::getShopEngineService();
        return $shopService->shopEngineClient();
    }


//    /**
//     * Alias to set the "offset" value of the query.
//     *
//     * @param  int  $value
//     * @return $this
//     */
//    public function skip($value)
//    {
//        return $this->offset($value);
//    }
//
//    /**
//     * Set the "offset" value of the query.
//     *
//     * @param  int  $value
//     * @return $this
//     */
//    public function offset($value)
//    {
//        $property = $this->unions ? 'unionOffset' : 'offset';
//
//        $this->$property = max(0, $value);
//
//        return $this;
//    }

}
