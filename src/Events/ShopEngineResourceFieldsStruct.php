<?php

namespace Brainspin\Novashopengine\Events;

use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Resource;

class ShopEngineResourceFieldsStruct
{
    public array $fields;


    public Model $model;

    /**
     * @var Resource
     */
    public Resource $resource;

    /**
     * ShopEngineResourceFieldsLoaded constructor.
     *
     * @param string $className
     * @param array $fields
     */
    public function __construct(Resource $resource, array $fields)
    {
        $this->fields = $fields;
        $this->resource = $resource;
        $this->model = $resource->model();
    }

    public function appendFields(array $fields) : self
    {
        $this->fields = array_merge($this->fields, $fields);
        return $this;
    }
}
