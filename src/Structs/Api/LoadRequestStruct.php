<?php

namespace ShopEngine\Nova\Structs\Api;

class LoadRequestStruct extends RequestStruct
{
    private string $id;

    /**
     * LoadRequestStruct constructor.
     *
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return array|string
     */
    public function createApiRequest()
    {
        return $this->id;
    }
}
