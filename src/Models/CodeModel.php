<?php

namespace ShopEngine\Nova\Models;

use SSB\Api\Model\Code;

class CodeModel extends ShopEngineModel
{
    public static $apiModel = Code::class;
    public static $apiEndpoint = 'code';

    /**
     * Define the attributes that should be merged with the attributes defined by swagger.
     * If a quantity is given the 'code' attribute will be generated in the api endpoint.
     *
     * @return array
     */
    public function getAdditionalPostAttributes(): array
    {
        $quantity = $this->getAttribute('quantity');

        if (is_numeric($quantity) && intval($quantity) > 1) {
            return [
                'code' => '',
                'quantity' => intval($quantity)
            ];
        }

        return [];
    }

//    public function codepools()
//    {
//        return $this->hasManyShopEngineEntities(CodepoolModel::class, 'codeId');
//    }
}
