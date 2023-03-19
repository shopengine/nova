<?php

use Illuminate\Support\Facades\Route;


Route::get('/klicktipps', 'KlicktippController@index');
Route::get('/klicktipps/{klicktipp}', 'KlicktippController@show');
Route::post('/klicktipps', 'KlicktippController@store');
Route::patch('/klicktipps', 'KlicktippController@update');

//TODO  convert it to a delete request
Route::get('/klicktipps/{tag}/delete', 'KlicktippController@destroy');



Route::post('/purchases/{resourceId}/manualJTL', 'PurchaseController@manualJTL');

// Special Cases
Route::post('/{resource}/{resourceId}/addOption', 'ShippingCostController@addOption');
Route::post('/{resource}/{resourceId}/removeOption', 'ShippingCostController@removeOption');

