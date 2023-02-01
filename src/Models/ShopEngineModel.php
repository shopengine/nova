<?php

namespace ShopEngine\Nova\Models;

use ArrayAccess;
use Illuminate\Support\Traits\ForwardsCalls;
use ShopEngine\Nova\Api\ListRequestBuilder;
use ShopEngine\Nova\Api\LoadRequestBuilder;
use ShopEngine\Nova\Api\StoreRequestBuilder;
use ShopEngine\Nova\Api\UpdateRequestBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Nova\Http\Requests\NovaRequest;
use ShopEngine\Nova\Structs\Api\ListRequestStruct;
use ShopEngine\Nova\Structs\Api\RequestFilterStruct;
use SSB\Api\Model\Article;
use SSB\Api\Model\ModelInterface;
use SSB\Api\Model\PaymentInformation;

class ShopEngineModel extends Model implements ArrayAccess, \JsonSerializable
{
    public ?ModelInterface $model;
    public static $apiModel;
    public static $apiEndpoint = null;

    public $seModel;
    public $useSwaggerTypesOnUpsert = true;

    /**
     * ShopEngineModel constructor.
     *
     * @param \SSB\Api\Model\ModelInterface|null $model
     */
    public function __construct(ModelInterface $model = null)
    {
        if ($model === null) {
            $model = new static::$apiModel();
        }

        // @todo remove me?
        $this->seModel = $model;

        $this->model = $model;
    }

    /**
     * @return string
     */
    public function getKeyName(): string
    {
        return isset($this->model::swaggerTypes()['aggregateId']) ? 'aggregateId' : 'id';
    }

    public function getId(): ?string
    {
        $key = $this->getKeyName();

        return $this->offsetGet($key);
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
     * @return mixed
     */
    public function offsetGet($offset): mixed
    {
        return $this->offsetExists($offset)
            ? $this->fromModel($offset)
            : null;
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
     * @todo fix this classes
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
        } elseif (is_array($obj)) {
            $newObj = [];

            foreach ($obj as $item) {
                try {
                    if (!is_array($item) && !is_null($item)) {
                        $interfaces = class_implements($item);
                        if (in_array(ModelInterface::class, $interfaces)) {
                            $newObj[] = $this->mapShopEngineValue($item);
                            continue;
                        }
                    }

                    $newObj[] = $item;
                } catch (\Exception $exception) {
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
     * @return \ShopEngine\Nova\Models\ShopEngineModel|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function first()
    {
        $request = app(NovaRequest::class);
        $builder = LoadRequestBuilder::fromResource($request->resource());

        return $builder->loadItem(
            $builder->buildFromRequest($request)
        );
    }

    /**
     * @param array $options
     */
    public function save(array $options = [])
    {
        $request = app(NovaRequest::class);

        if ($request->get('editMode') === 'update') {
            (UpdateRequestBuilder::fromResource($request->resource()))->save($this);
            return;
        }

        if ($request->get('editMode') === 'create') {
            (StoreRequestBuilder::fromResource($request->resource()))->save($this);
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

        throw (new ModelNotFoundException())->setModel(get_class($this));
    }

    public function findOrFail($id): ?Model
    {
        $model = $this->find($id);

        if (!is_null($model)) {
            return $model;
        }

        throw (new ModelNotFoundException())->setModel(get_class($this->baseQuery()));
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
    public function newQuery()
    {
        $request = app(NovaRequest::class);

        return LoadRequestBuilder::fromResource($request->resource());
    }

    public function getUpdatedAtColumn()
    {
    }

    public function getOriginal($key = null, $default = null)
    {
        return [];
    }

    public function getRawOriginal($key = null, $default = null)
    {
        return [];
    }

    public function getDirty()
    {
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
    public function find($id)
    {
        $builder = new LoadRequestBuilder($this);

        return $builder->find($id);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        if (!isset($this->model::setters()[$offset])) {
            throw \Exception('Cant find setter for  '.$offset. ' on '. get_class($this->model));
        }

        $this->model->{$this->model::setters()[$offset]}($value);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
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

    /**
     * Define a one-to-many relationship.
     *
     * @param  string  $related
     * @param  string|null  $foreignKey
     * @param  string|null  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hasManyShopEngineEntities(string $related, string $apiIdentifier)
    {
        return $this->createHasManyRequest($related, $apiIdentifier);

//      @todo - check if somewhere is still needed to return a collection, otherwise merge with
//      createHasManyRequest

//        $builder =  ListRequestBuilder::fromClass($related);
//        $listRequest = $this->createHasManyRequest($related, $apiIdentifier);
//        return $builder->loadItems($listRequest);
    }

    public function createHasManyRequest(string $related, string $apiIdentifier): ListRequestStruct
    {
        $listRequest = new ListRequestStruct();
        $listRequest->addFilter(new RequestFilterStruct(
            $apiIdentifier,
            $this->getId(),
            'eq'
        ));

        return $listRequest;
    }

    /**
     * Merge additional attributes that are not defined via swagger
     *
     * @param array $attributes
     * @return array
     */
    public function mergePostAttributes(array $attributes): array
    {
        return array_merge($attributes, $this->getAdditionalPostAttributes());
    }

    /**
     * Define the attributes that should be merged with the attributes defined by swagger
     *
     * @return array
     */
    public function getAdditionalPostAttributes(): array
    {
        return [];
    }
}
