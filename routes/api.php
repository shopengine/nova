<?php

use Illuminate\Support\Facades\Route;

Route::post('/purchases/{resourceId}/manualJTL', 'PurchaseController@manualJTL');

Route::get('/codeless', 'CodelessController@index');
Route::get('/codeless/{id}', 'CodelessController@show');
Route::patch('/codeless', 'CodelessController@update');

// Special Cases
Route::post('/{resource}/{resourceId}/addOption', 'ShippingCostController@addOption');
Route::post('/{resource}/{resourceId}/removeOption', 'ShippingCostController@removeOption');

