<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'shop'], function () {
    Route::group(['prefix' => 'marketing-provider'], function () {
        Route::group(['prefix' => 'klicktipp'], function () {
            Route::group(['prefix' => 'period-tags'], function () {
                Route::get('/', 'MarketingProvider\KlicktippPeriodTagController@index');
                Route::get('/options', 'MarketingProvider\KlicktippPeriodTagController@options');
                Route::get('/{tag}', 'MarketingProvider\KlicktippPeriodTagController@show');
                Route::post('/', 'MarketingProvider\KlicktippPeriodTagController@store');
                Route::patch('/', 'MarketingProvider\KlicktippPeriodTagController@update');
                Route::post('/{tag}/delete', 'MarketingProvider\KlicktippPeriodTagController@destroy');
            });
        });
    });
});

Route::post('/purchases/{resourceId}/manualJTL', 'PurchaseController@manualJTL');

// Special Cases
Route::post('/{resource}/{resourceId}/addOption', 'ShippingCostController@addOption');
Route::post('/{resource}/{resourceId}/removeOption', 'ShippingCostController@removeOption');
Route::patch('/codeless/toggle-status', 'CodelessController@toggleStatus');
