<?php

Route::group(['namespace' => 'Dorcas\ModulesAssistant\Http\Controllers', 'middleware' => ['web','auth'], 'prefix' => 'mas'], function() {
    Route::get('assistant-main', 'ModulesAssistantController@index')->name('assistant-main');
    Route::get('assistant-generate/{module}/{url}', 'ModulesAssistantController@generate');

    Route::get('generate-docs/{tag}', 'ModulesAssistantController@generateDocs');

    Route::post('/message-send', 'ModulesAssistantController@helpSendMessage');
});


?>