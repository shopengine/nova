<?php

namespace ShopEngine\Nova\Structs\Api;

abstract class RequestStruct
{
    /**
     * @return array|string
     */
    public function createApiRequest()
    {
        return [];
    }
}
