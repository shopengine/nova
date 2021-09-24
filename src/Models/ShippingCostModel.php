<?php namespace ShopEngine\Nova\Models;

use SSB\Api\Model\ShippingCost;

class ShippingCostModel extends ShopEngineModel
{
    public static $apiModel = ShippingCost::class;
    public static $apiEndpoint = 'shippingcost';
}
