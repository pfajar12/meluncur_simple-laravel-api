<?php

use Illuminate\Http\Request;

Route::group(['middleware' => ['api']], function(){

	// get role
	Route::get('/get-role', 'Api\UserController@get_role');
	// register
	Route::post('/register', 'Api\UserController@register');
	// login
	Route::post('/login', 'Api\UserController@login');

	// GALON
	// get galon type
	Route::get('/get-galon-type', 'Api\GalonController@get_galon_type');


	Route::group(['middleware' => ['jwt.auth']], function(){

		// PROFILE
		// get profile
		Route::get('/profile', 'Api\UserController@profile');
		// update profile
		Route::post('/profile-update', 'Api\UserController@profile_update');
		// set alamat & lat lng
		Route::post('/set-address', 'Api\UserController@set_address');
		// change password
		Route::post('/change-password', 'Api\UserController@change_password');
		// view client data
		Route::post('/view-client-data', 'Api\UserController@view_client_data');
		// upload business licence
		Route::post('/upload-business-licence', 'Api\UserController@upload_business_licence');
		// upload business licence
		Route::post('/upload-business-place', 'Api\UserController@upload_business_place');

		// GALON
		// set galon type
		Route::post('/set-galon-type', 'Api\GalonController@set_galon_type');
		// search nearest galon vendor
		Route::post('/search-galon-vendor', 'Api\GalonController@search_galon_vendor');
		// order
		Route::post('/galon-order', 'Api\GalonController@create_order');
		// order list user view
		Route::post('/galon-order-list-user-view', 'Api\GalonController@order_list_user_view');
		// order list vendor view
		Route::post('/galon-order-list-vendor-view', 'Api\GalonController@order_list_vendor_view');
		// accept galon order
		Route::post('/galon-order-accept', 'Api\GalonController@galon_order_accept');
		// cancel galon order
		Route::post('/galon-order-cancel', 'Api\GalonController@galon_order_cancel');
		// approve galon order
		Route::post('/galon-order-approve', 'Api\GalonController@galon_order_approve');
		// order log vendor view
		Route::post('/galon-order-log-vendor-view', 'Api\GalonController@order_log_vendor_view');
		// order log user view
		Route::post('/galon-order-log-user-view', 'Api\GalonController@order_log_user_view');

		// LAUNDRY
		// search nearest laundry vendor
		Route::post('/search-laundry-vendor', 'Api\LaundryController@search_laundry_vendor');
		// order
		Route::post('/laundry-order', 'Api\LaundryController@create_order');
		// order list user view
		Route::post('/laundry-order-list-user-view', 'Api\LaundryController@order_list_user_view');
		// order list vendor view
		Route::post('/laundry-order-list-vendor-view', 'Api\LaundryController@order_list_vendor_view');
		// accept laundry order
		Route::post('/laundry-order-accept', 'Api\LaundryController@laundry_order_accept');
		// approve laundry order
		Route::post('/laundry-order-approve', 'Api\LaundryController@laundry_order_approve');
		// cancel laundry order
		Route::post('/laundry-order-cancel', 'Api\LaundryController@laundry_order_cancel');
		// order log vendor view
		Route::post('/laundry-order-log-vendor-view', 'Api\LaundryController@order_log_vendor_view');
		// order log user view
		Route::post('/laundry-order-log-user-view', 'Api\LaundryController@order_log_user_view');

		// AC
		// search nearest ac vendor
		Route::post('/search-ac-vendor', 'Api\AcController@search_ac_vendor');
		// order
		Route::post('/ac-order', 'Api\AcController@create_order');
		// order list user view
		Route::post('/ac-order-list-user-view', 'Api\AcController@order_list_user_view');
		// order list vendor view
		Route::post('/ac-order-list-vendor-view', 'Api\AcController@order_list_vendor_view');
		// accept ac order
		Route::post('/ac-order-accept', 'Api\AcController@ac_order_accept');
		// approve ac order
		Route::post('/ac-order-approve', 'Api\AcController@ac_order_approve');
		// cancel ac order
		Route::post('/ac-order-cancel', 'Api\AcController@ac_order_cancel');
		// order log vendor view
		Route::post('/ac-order-log-vendor-view', 'Api\AcController@order_log_vendor_view');
		// order log user view
		Route::post('/ac-order-log-user-view', 'Api\AcController@order_log_user_view');

		// CCTV
		// search nearest cctv vendor
		Route::post('/search-cctv-vendor', 'Api\CCTVController@search_cctv_vendor');
		// order
		Route::post('/cctv-order', 'Api\CCTVController@create_order');
		// order list user view
		Route::post('/cctv-order-list-user-view', 'Api\CCTVController@order_list_user_view');
		// order list vendor view
		Route::post('/cctv-order-list-vendor-view', 'Api\CCTVController@order_list_vendor_view');
		// accept cctv order
		Route::post('/cctv-order-accept', 'Api\CCTVController@cctv_order_accept');
		// approve cctv order
		Route::post('/cctv-order-approve', 'Api\CCTVController@cctv_order_approve');
		// cancel cctv order
		Route::post('/cctv-order-cancel', 'Api\CCTVController@cctv_order_cancel');
		// order log vendor view
		Route::post('/cctv-order-log-vendor-view', 'Api\CCTVController@order_log_vendor_view');
		// order log user view
		Route::post('/cctv-order-log-user-view', 'Api\CCTVController@order_log_user_view');

	});

});
