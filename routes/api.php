<?php

use Illuminate\Support\Facades\Route;

Route::patch('/purchases/{resourceId}/origin-status', 'PurchaseController@setOriginStatus');

Route::post('/{resource}/{resourceId}/addOption', 'ShippingCostController@addOption');
Route::post('/{resource}/{resourceId}/removeOption', 'ShippingCostController@removeOption');
