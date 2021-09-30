<?php namespace ShopEngine\Nova\Models;

use SSB\Api\Model\Code;

class CodeModel extends ShopEngineModel
{
    public static $apiModel = Code::class;
    public static $apiEndpoint = 'code';

//    public function codepools()
//    {
//        return $this->hasManyShopEngineEntities(CodepoolModel::class, 'codeId');
//    }
}
