<?php

namespace ShopEngine\Nova\Structs;

abstract class Struct
{
    public static function createFrom(Struct $object)
    {
        try {
            $self = (new \ReflectionClass(static::class))
                ->newInstanceWithoutConstructor();
        } catch (\ReflectionException $exception) {
            throw new \InvalidArgumentException($exception->getMessage());
        }

        foreach ($object->getVars() as $property => $value) {
            $self->set($property, $value);
        }

        return $self;
    }

    public static function createFromObject($object) : Struct
    {
        try {
            $reflection = (new \ReflectionClass(static::class));
            $self = new static();
        } catch (\ReflectionException $exception) {
            throw new \InvalidArgumentException($exception->getMessage());
        }

        foreach ($reflection->getProperties() as $reflectionProperty) {
            $propName = $reflectionProperty->getName();
            if (isset($object->$propName)) {
                $self->$propName = $object->$propName;
            }
        }

        return $self;
    }

    public static function createFromArray($arr)
    {
        try {
            $reflection = (new \ReflectionClass(static::class));
            //$self = $reflection->newInstanceWithoutConstructor();
            $self = new static();
        } catch (\ReflectionException $exception) {
            throw new \InvalidArgumentException($exception->getMessage());
        }

        foreach ($reflection->getProperties() as $reflectionProperty) {
            $propName = $reflectionProperty->getName();
            if (!isset($arr[$propName])) {
                throw new \Exception($propName . ' not exists in array');
            }
            $self->$propName = $arr[$propName];
        }

        return $self;
    }

    public function assign(array $options): Struct
    {
        foreach ($options as $key => $value) {
            if ($key === 'id' && method_exists($this, 'setId')) {
                $this->setId($value);
                continue;
            }

            try {
                $this->$key = $value;
            } catch (\Exception $error) {
                // @todo maybe more later
                throw $error;
            }
        }

        return $this;
    }

    public function getVars(): array
    {
        return get_object_vars($this);
    }
}
