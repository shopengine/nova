<?php

namespace ShopEngine\Nova\Structs;

class ArrayStruct extends Struct implements \ArrayAccess
{
    /**
     * @var array
     */
    protected $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function has(string $property): bool
    {
        return \array_key_exists($property, $this->data);
    }

    public function offsetExists($offset)
    {
        return \array_key_exists($offset, $this->data);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->data[$offset]);
    }

    public function get(string $key)
    {
        return $this->offsetGet($key);
    }

    /**
     * @param string|int $key
     * @param array      $value
     *
     * @return array
     */
    public function set($key, $value)
    {
        return $this->data[$key] = $value;
    }

    public function assign(array $options): Struct
    {
        $this->data = array_replace_recursive($this->data, $options);

        return $this;
    }

    public function all()
    {
        return $this->data;
    }

    public function getVars(): array
    {
        return $this->data ?: [];
    }
}
