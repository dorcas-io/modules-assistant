<?php

Route::group(['namespace' => 'Dorcas\ModulesAssistant\Http\Controllers', 'middleware' => ['web']], function() {
    Route::get('sales', 'ModulesAssistantController@index')->name('sales');
});


?>