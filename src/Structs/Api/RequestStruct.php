<?php

namespace Brainspin\Novashopengine\Structs\Api;

abstract class RequestStruct {

    /**
     * @return array|string
     */
    public function createApiRequest()
    {
        return [];
    }
}
