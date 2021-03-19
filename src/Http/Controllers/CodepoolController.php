<?php

namespace Brainspin\Novashopengine\Http\Controllers;

use function Sentry\captureException;

class CodepoolController extends ShopEngineNovaController
{
    public function archive(string $id = '')
    {
        try {
            $this->getClient()->post("codepool/archive/$id", []);

            return [
                'status' => 'ok'
            ];
        }
        catch (\Exception $e) {
            captureException($e);

            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}
