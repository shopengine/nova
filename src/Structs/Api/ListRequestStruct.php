<?php

namespace Brainspin\Novashopengine\Structs\Api;

class ListRequestStruct extends RequestStruct {

    /**
     * @var string
     */
    private string $pageSize = "25";

    /**
     * @todo may make this also a string
     * @var int
     */
    private int $page = 0;

    /**
     * @var string
     */
    private string $sort;

    /**
     * @var boolean
     */
    private bool $sortDescending = false;

    /**
     * @var string[]
     */
    private array $properties = [];

    /**
     * @var RequestFilterStruct[]
     */
    private array $filters = [];

    /**
     * @return array
     */
    public function createApiRequest()
    {
        $request =  [
            'pageSize' => $this->pageSize,
            'page' => $this->page,
            'sort' => ($this->sortDescending ? '-' : '') . $this->sort,
        ];

        if (count($this->properties) > 0) {
            $request['properties'] = join('|',$this->properties);
        }

        if (count($this->filters) > 0) {
            $filters = array_map(fn(RequestFilterStruct $requestFilter) => $requestFilter->getRequest() , $this->filters);

            foreach ($filters as $filter) {
                $request += $filter;
            }
        }

        return $request;
    }

    /**
     * @return array
     */
    public function createCountRequest() : array
    {
        $request = $this->createApiRequest();

        return array_filter(
            $request,
            fn($key) => !in_array($key, [
                'pageSize',
                'page',
                'sort'
            ]), ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * @param int $page
     */
    public function setPage(int $page): void
    {
        $this->page = $page;
    }

    /**
     * @param string $pageSize
     */
    public function setPageSize(string $pageSize): void
    {
        $this->pageSize = $pageSize;
    }

    /**
     * @param bool $sortDescending
     */
    public function setSortDescending(bool $sortDescending): void
    {
        $this->sortDescending = $sortDescending;
    }

    /**
     * @param string[] $properties
     */
    public function setProperties(array $properties): void
    {
        $this->properties = $properties;
    }

    /**
     * @param RequestFilterStruct $filter
     */
    public function addFilter(RequestFilterStruct $filter): void
    {
        $this->filters[] = $filter;
    }

    /**
     * @param string $sort
     */
    public function setSort(string $sort): void
    {
        $this->sort = $sort;
    }
}
