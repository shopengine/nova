<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Contracts
    |--------------------------------------------------------------------------
    |
    | Define the contract that resolves the shop and it's settings.
    |
    */

    'shopengine_nova_interface' => 'ShopEngine\Nova\Contracts\ShopEngineNovaInterface',

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    |
    | Define the order of the navigation groups.
    |
    */

    'navigation' => [
        'base',
        'codes',
        'stats'
    ]
];
