<?php namespace ShopEngine\Nova\Models;

use ShopEngine\Nova\Api\ListRequestBuilder;
use ShopEngine\Nova\Structs\Api\ListRequestStruct;
use ShopEngine\Nova\Structs\Api\RequestFilterStruct;
use SSB\Api\Model\Codepool;

class CodepoolModel extends ShopEngineModel
{
    public static $apiModel = Codepool::class;
    public static $apiEndpoint = 'codepool';

    public function codes()
    {
        return $this->hasManyShopEngineEntities(CodeModel::class, 'codepoolId');
    }
}
