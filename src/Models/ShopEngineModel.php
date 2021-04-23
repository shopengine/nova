<?php

namespace Brainspin\Novashopengine\Models;

use ArrayAccess;
use Brainspin\Novashopengine\Api\StoreRequestBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Laravel\Nova\Http\Requests\NovaRequest;
use SSB\Api\Model\Article;
use SSB\Api\Model\ModelInterface;
use SSB\Api\Model\PaymentInformation;

class ShopEngineModel implements ArrayAccess, \JsonSerializable
{
    public ModelInterface $model;
    public static $apiModel;

    public function __construct(ModelInterface $model = null)
    {
        if ($model === null) {
            $model = new static::$apiModel;
        }

        $this->model = $model;
    }


    public function getKeyName()
    {
        return isset($this->model::swaggerTypes()['aggregateId']) ? 'aggregateId' : 'id';
    }

    public function offsetExists($offset)
    {
        return isset($this->model::getters()[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ?
            $this->fromModel($offset) :
            null;
    }

    public function __get($key)
    {
        if ($this->offsetExists($key)) {
            $getter = $this->model::getters()[$key];
            return $this->model->{$getter}();
        }

        return null;
    }

    public function attributesToArray()
    {
        return $this->mapShopEngineValue($this->model);
    }

    private function fromModel($offset)
    {
        $data = $this->model->{$this->model::getters()[$offset]}();
        return $this->mapShopEngineModelToArray($data);
    }

    private function mapShopEngineValue(ModelInterface $entry)
    {
        $responseEntry = [];

        $getProperties = $entry::getters();

        $hideNullValues = $entry instanceof PaymentInformation;

        foreach ($getProperties as $getKey => $getProperty) {
            $obj = $entry->{$getProperty}();
            $value = $this->mapShopEngineModelToArray($obj);

            if (
                $hideNullValues === false ||
                ($hideNullValues && $value !== null)
            ) {
                $responseEntry[$getKey] = $value;
            }
        }

        return $responseEntry;
    }

    private function mapShopEngineModelToArray($obj)
    {
        if (is_object($obj)) {
            switch (get_class($obj)) {
                case Article::class:
                    /** @var Article $obj */
                    $obj = $obj->getSku();
                    break;
            }

            if (is_object($obj)) {
                $interfaces = class_implements($obj);
                if (in_array(ModelInterface::class, $interfaces)) {
                    $obj = $this->mapShopEngineValue($obj);
                }
            }
        }
        else if (is_array($obj)) {
            $newObj = [];

            foreach ($obj as $item) {
                try {
                    $interfaces = class_implements($item);
                    if (in_array(ModelInterface::class, $interfaces)) {
                        $newObj[] = $this->mapShopEngineValue($item);
                    }
                    else {
                        $newObj[] = $item;
                    }
                }
                catch (\Exception $exception) {
                    $newObj[] = $item;
                }
            }

            $obj = $newObj;
        }

        return $obj;
    }

    public function jsonSerialize()
    {
        $obj = [];
        foreach ($this->model::getters() as $attribute => $getter) {
            $obj[$attribute] = $this->model->{$getter}();
        }

        return $obj;
    }

    public function save(array $options = [])
    {
        $request = app(NovaRequest::class);
        return (new StoreRequestBuilder($request))->save($this);
    }


    // for faking an eloquent model

    /**
     * @return mixed
     */
    public function newQuery() {
        return null;
    }

    public function newQueryWithoutScopes()
    {
        return $this;
    }

    public function find()
    {
        return $this;
    }

    public function offsetSet($offset, $value){

    }

    public function offsetUnset($offset)
    {

    }

    public function whereKey()
    {
        return null;
    }

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        return static::class;
    }

    /**
     * Get the value of the model's primary key.
     *
     * @return mixed
     */
    public function getKey()
    {
        return '0';
    }
}
