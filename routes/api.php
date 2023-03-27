<?php

use Illuminate\Support\Facades\Route;


Route::group(['prefix' => '/settings'], function(){
    Route::group(['prefix' => '/newsletter-provider'], function(){
        Route::group(['prefix' => 'klicktipp'], function(){
            Route::group(['prefix' => 'tags-periods'],function(){
                Route::get('/', 'KlicktippTagsPeriodsController@index')->name('settings.newsletter-provider.klicktipp.tags-periods.index');
                Route::get('{tag}', 'KlicktippTagsPeriodsController@show')->name('settings.newsletter-provider.klicktipp.tags-periods.show');
                Route::get('{tag}/edit', 'KlicktippTagsPeriodsController@edit')->name('settings.newsletter-provider.klicktipp.tags-periods.edit');
                Route::post('/', 'KlicktippTagsPeriodsController@store');
                Route::patch('/', 'KlicktippTagsPeriodsController@update');
                Route::post('/{tag}/delete', 'KlicktippTagsPeriodsController@destroy');
            });
        });
    });
});




Route::post('/purchases/{resourceId}/manualJTL', 'PurchaseController@manualJTL');

// Special Cases
Route::post('/{resource}/{resourceId}/addOption', 'ShippingCostController@addOption');
Route::post('/{resource}/{resourceId}/removeOption', 'ShippingCostController@removeOption');

