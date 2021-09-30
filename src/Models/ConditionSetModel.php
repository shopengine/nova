<?php

namespace ShopEngine\Nova\Models;

use SSB\Api\Model\ConditionSet;

class ConditionSetModel extends ShopEngineModel
{
    public static $apiModel = ConditionSet::class;
    public static $apiEndpoint = 'conditionset';

    public $useSwaggerTypesOnUpsert = false;
}
