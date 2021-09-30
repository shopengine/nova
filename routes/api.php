<?php

use Illuminate\Support\Facades\Route;

Route::post('/purchases/{resourceId}/manualJTL', 'PurchaseController@manualJTL');


// Special Cases
Route::post('/{resource}/{resourceId}/addOption', 'ShippingCostController@addOption');
Route::post('/{resource}/{resourceId}/removeOption', 'ShippingCostController@removeOption');
