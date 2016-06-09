<?php 

Route::group(['prefix' => 'sms', 'namespace' => '\Selmonal\SMS', 'middleware' => ['auth']], function() {

	Route::get('compose', [
		'as' => 'sms.compose', 'uses' => 'SMSController@compose'
	]);

	Route::get('log', [
		'as' => 'sms.log', 'uses' => 'SMSController@log'
	]);

	Route::get('clear', [
		'as' => 'sms.clear', 'uses' => 'SMSController@clear'
	]);

	Route::post('send', [
		'as' => 'sms.send', 'uses' => 'SMSController@send'
	]);

});