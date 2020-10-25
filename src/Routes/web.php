<?php
Route::group(['middleware' => 'web', 'namespace' => '\SpiritSystems\DayByDay\Core\Http\Controllers'], function () {
    /**
     * Leads
     */
    Route::get('/leads/alldata', 'LeadsController@allData')->name('leads.allData');
    Route::post('upload/lead/{client}', 'LeadsController@upload')->name('document.lead.upload');
    Route::group(['prefix' => 'leads'], function () {
        Route::post('/leads/{lead}/probability', 'LeadsController@updateProbability')->name('leads.updateProbability');
    });
});
