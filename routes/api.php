<?php

use Illuminate\Support\Facades\Route;

//Route::get('/{resource}/{resourceId}/stats', 'StatisticController@stats');
//Route::get('/{resource}/{resourceId}/stats-by-time', 'StatisticController@statsByTime');
//
//Route::get('/{resource}/{resourceId}/export/orders', 'ExportController@orders');
//Route::get('/{resource}/{resourceId}/export/newCustomers', 'ExportController@newCustomer');
//Route::get('/codepools/{resourceId}/export/activeCodes', 'ExportController@codepoolActiveCodes');
//
//Route::get('/codepools/{resourceId}/archive', 'CodepoolController@archive');
//Route::post('/purchases/{resourceId}/manualJTL', 'PurchaseController@manualJTL');

Route::get('/lastCodes', 'IndexController@lastCodes');

//Route::get('/{resource}/filters', 'IndexController@filter');
//
//Route::get('/{resource}', 'IndexController@index');
//Route::get('/{resource}/{resourceId}', 'DetailController@show');
//
//Route::post('/{resource}', 'StoreController@store');
//Route::get('/{resource}/{resourceId}/update-fields', 'UpdateController@fields');
//Route::put('/{resource}/{resourceId}', 'UpdateController@store');
//
//// Special Cases
//Route::post('/{resource}/{resourceId}/addOption', 'ShippingCostController@addOption');
//Route::post('/{resource}/{resourceId}/removeOption', 'ShippingCostController@removeOption');
//
