<?php

namespace ShopEngine\Nova\Models;

use SSB\Api\Model\Codepool;

class CodepoolModel extends ShopEngineModel
{
    public static $apiModel = Codepool::class;
    public static $apiEndpoint = 'codepool';

    protected $primaryKey = 'aggregateId';
    protected $keyType = 'string';

    public function codes()
    {
        return $this->hasManyShopEngineEntities(CodeModel::class, 'codepoolId');
    }
}
