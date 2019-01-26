<?php

use Illuminate\Http\Request;

Route::group(['middleware' => ['api']], function(){

	// get role
	Route::get('/get-role', 'Api\UserController@get_role');
	// // register
	// Route::post('/register', 'Api\RegisterController@register');
	// // login
	// Route::post('/login', 'Api\LoginController@login');
	// // depot list
	// Route::post('/depot-list', 'Api\DepotController@show_list');
	// // search depot
	// Route::post('/search-depot', 'Api\OrderController@search_depot');
	// // get galon type
	// Route::post('/get-galon-type', 'Api\UserController@get_galon_type');


	// Route::group(['middleware' => ['jwt.auth']], function(){

	// 	// profile
	// 	Route::post('/profile', 'Api\UserController@profile');
	// 	Route::post('/profile-update', 'Api\UserController@profile_update');
	// 	Route::post('/change-location', 'Api\UserController@change_location');
	// 	Route::post('/set-galon-type', 'Api\UserController@set_galon_type');
	// 	Route::post('/change-password', 'Api\UserController@change_password');

	// 	// order
	// 	Route::post('/order', 'Api\OrderController@create_order');
	// 	Route::post('/order-list-client', 'Api\OrderController@order_list_client');
	// 	Route::post('/order-list-depot', 'Api\OrderController@order_list_depot');
	// 	Route::post('/approve-order', 'Api\OrderController@approve_order');
	// 	Route::post('/cancel-order', 'Api\OrderController@cancel_order');
	// 	Route::post('/order-log-client', 'Api\OrderController@order_log_client');
	// 	Route::post('/order-log-depot', 'Api\OrderController@order_log_depot');

	// });

});
