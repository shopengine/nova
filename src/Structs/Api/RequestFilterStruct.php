<?php

namespace ShopEngine\Nova\Structs\Api;


// @todo: on php8 use constructor property promotion

final class RequestFilterStruct {
    /**
     * @var string
     */
    private string $field;

    /**
     * @var string
     */
    private string $value;

    /**
     * @var boolean
     */
    private ?string $operator;

    /**
     * RequestOperationStruct constructor.
     *
     * @param string $field
     * @param string $value
     * @param null $operator
     */
    public function __construct(string $field, string $value, $operator = null)
    {
        $this->name = $field;
        $this->value = $value;
        $this->operator = $operator;
    }

    /**
     * @return string[]
     */
    public function getRequest() : array
    {
        $key = $this->name . ($this->operator ? '-' . $this->operator : '');
        return [
            $key => $this->value
        ];
    }
}
