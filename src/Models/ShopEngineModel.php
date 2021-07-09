<?php

namespace Brainspin\Novashopengine\Models;

use ArrayAccess;
use Brainspin\Novashopengine\Api\LoadRequestBuilder;
use Brainspin\Novashopengine\Api\StoreRequestBuilder;
use Brainspin\Novashopengine\Api\UpdateRequestBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Nova\Http\Requests\NovaRequest;
use SSB\Api\Model\Article;
use SSB\Api\Model\ModelInterface;
use SSB\Api\Model\PaymentInformation;

abstract class ShopEngineModel extends Model implements ArrayAccess, \JsonSerializable
{
    public ?ModelInterface $model;
    public static $apiModel;

    /**
     * ShopEngineModel constructor.
     *
     * @param \SSB\Api\Model\ModelInterface|null $model
     */
    public function __construct(ModelInterface $model = null)
    {
        if ($model === null) {
            $model = new static::$apiModel;
        }

        $this->model = $model;
    }

    /**
     * @return string
     */
    public function getKeyName(): string
    {
        return isset($this->model::swaggerTypes()['aggregateId']) ? 'aggregateId' : 'id';
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->model::getters()[$offset]);
    }

    /**
     * @param mixed $offset
     *
     * @return array|mixed|\SSB\Api\Model\Article|string|null
     */
    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ?
            $this->fromModel($offset) :
            null;
    }

    /**
     * @param $key
     *
     * @return null
     */
    public function __get($key)
    {
        if ($this->offsetExists($key)) {
            $getter = $this->model::getters()[$key];
            return $this->model->{$getter}();
        }

        return null;
    }

    /**
     * @return array
     */
    public function attributesToArray(): array
    {
        return $this->mapShopEngineValue($this->model);
    }

    /**
     * @param $offset
     *
     * @return array|\SSB\Api\Model\Article|string
     */
    private function fromModel($offset)
    {
        $data = $this->model->{$this->model::getters()[$offset]}();
        return $this->mapShopEngineModelToArray($data);
    }

    /**
     * @param \SSB\Api\Model\ModelInterface $entry
     *
     * @return array
     */
    private function mapShopEngineValue(ModelInterface $entry): array
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

    /**
     * @param $obj
     *
     * @return array|\SSB\Api\Model\Article|string
     */
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

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $obj = [];
        foreach ($this->model::getters() as $attribute => $getter) {
            $obj[$attribute] = $this->model->{$getter}();
        }

        return $obj;
    }

    /**
     * @return \Brainspin\Novashopengine\Models\ShopEngineModel|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function first()
    {
        $request = app(NovaRequest::class);
        $builder = new LoadRequestBuilder($request);
        return $builder->loadItem(
            $builder->buildFromRequest()
        );
    }


    /**
     * @param array $options
     */
    public function save(array $options = [])
    {
        $request = app(NovaRequest::class);

        if ($request->get('editMode') === 'update') {
            (new UpdateRequestBuilder($request))->save($this);
            return;
        }

        if ($request->get('editMode') === 'create') {
            (new StoreRequestBuilder($request))->save($this);
            return;
        }
    }

    /** -- for faking an eloquent model --  */

    /**
     * Execute the query and get the first result or throw an exception.
     *
     * @param  array  $columns
     * @return \Illuminate\Database\Eloquent\Model|static
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function firstOrFail()
    {
        if (! is_null($model = $this->first())) {
            return $model;
        }

        throw (new ModelNotFoundException)->setModel(get_class($this));
    }

    public function lockForUpdate()
    {
        return $this;
    }

    public function lock($value = true)
    {

    }

    /**
     * @return mixed
     */
    public function newQuery() {
        return null;
    }

    public function getUpdatedAtColumn() {

    }

    public function getOriginal($key = null, $default = null) {
        return [];
    }

    public function getRawOriginal($key = null, $default = null)
    {
        return [];
    }

    public function getDirty() {
        return parent::getDirty();
    }

    /**
     * @return $this
     */
    public function newQueryWithoutScopes(): ShopEngineModel
    {
        return $this;
    }

    /**
     * @return $this
     */
    public function find()
    {
        return $this;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value){

    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {

    }

    /**
     * @return $this
     */
    public function whereKey(): ShopEngineModel
    {
        return $this;
    }

    /**
     * @return string
     */
    public function getMorphClass()
    {
        return static::class;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return '0';
    }
}
