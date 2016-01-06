<?php

/**
*
* Routes For Protected User
*
*
* Here is where you can register all of the routes for protected resources who can be accessed based on ACL.
*
* STAFF AREA
*
* Product Resources 		: Line 40 - 67 
*	
* Product Warehouse Data 	: Line 73 - 89
*	
* Cluster Resources 		: Line 94 - 117
*	
* Supplier Resources 		: Line 123 - 145
*	
* Purhcase Resources 		: Line 151 - 168
*	
* Sale Resources 			: Line 173 - 189
*	
* Courier Resources 		: Line 195 - 218
*	
* MANAGER AREA
*	
* Voucher Resources 		: Line 226 - 242
*	
* Point Resources 			: Line 248 - 258
*	
* Setting Resources 		: Line 264 - 280
*	
* ADMIN AREA
*	
* Administrator Resources	: Line 289 - 3016
*/

$app->group(['middleware' => 'oauth|staff', 'namespace' => 'App\Http\Controllers'], function ($app) 
{
	// ------------------------------------------------------------------------------------
	// PRODUCTS
	// ------------------------------------------------------------------------------------

	$app->get('/products',
		[
			'uses'				=> 'ProductController@index'
		]
	);

	$app->get('/product/{id}',
		[
			'uses'				=> 'ProductController@detail'
		]
	);

	$app->post('/product/store',
		[
			'uses'				=> 'ProductController@store'
		]
	);

	$app->get('/product/delete/{id}',
		[
			'uses'				=> 'ProductController@delete'
		]
	);

	// ------------------------------------------------------------------------------------
	// PRODUCT WAREHOUSE
	// ------------------------------------------------------------------------------------

	$app->get('/product/stock/card/{id}',
		[
			'uses'				=> 'WarehouseController@card'
		]
	);

	$app->get('/products/stock/critical',
		[
			'uses'				=> 'WarehouseController@critical'
		]
	);

	$app->get('/products/stock/opname',
		[
			'uses'				=> 'WarehouseController@opname'
		]
	);

	// ------------------------------------------------------------------------------------
	// CLUSTERS
	// ------------------------------------------------------------------------------------

	$app->get('/clusters',
		[
			'uses'				=> 'ClusterController@index'
		]
	);

	$app->get('/cluster/{id}',
		[
			'uses'				=> 'ClusterController@detail'
		]
	);

	$app->post('/cluster/store',
		[
			'uses'				=> 'ClusterController@store'
		]
	);

	$app->delete('/cluster/delete/{id}',
		[
			'uses'				=> 'ClusterController@delete'
		]
	);

	// ------------------------------------------------------------------------------------
	// SUPPLIERS
	// ------------------------------------------------------------------------------------

	$app->get('/suppliers',
		[
			'uses'				=> 'SupplierController@index'
		]
	);

	$app->get('/supplier/{id}',
		[
			'uses'				=> 'SupplierController@detail'
		]
	);

	$app->post('/supplier/store',
		[
			'uses'				=> 'SupplierController@store'
		]
	);

	$app->delete('/supplier/delete/{id}',
		[
			'uses'				=> 'SupplierController@delete'
		]
	);

	// ------------------------------------------------------------------------------------
	// PURCHASES
	// ------------------------------------------------------------------------------------

	$app->get('/purchases',
		[
			'uses'				=> 'PurchaseController@index'
		]
	);

	$app->get('/purchase/{id}',
		[
			'uses'				=> 'PurchaseController@detail'
		]
	);

	$app->post('/purchase/store',
		[
			'uses'				=> 'PurchaseController@store'
		]
	);

	// ------------------------------------------------------------------------------------
	// SALES
	// ------------------------------------------------------------------------------------

	$app->get('/sales',
		[
			'uses'				=> 'SaleController@index'
		]
	);

	$app->get('/sale/{id}',
		[
			'uses'				=> 'SaleController@detail'
		]
	);

	$app->post('/sale/update/status',
		[
			'uses'				=> 'SaleController@status'
		]
	);

	// ------------------------------------------------------------------------------------
	// COURIERS
	// ------------------------------------------------------------------------------------

	$app->get('/couriers',
		[
			'uses'				=> 'CourierController@index'
		]
	);

	$app->get('/courier/{id}',
		[
			'uses'				=> 'CourierController@detail'
		]
	);

	$app->post('/courier/store',
		[
			'uses'				=> 'CourierController@store'
		]
	);

	$app->delete('/courier/delete/{id}',
		[
			'uses'				=> 'CourierController@delete'
		]
	);
});

$app->group(['middleware' => 'oauth|manager', 'namespace' => 'App\Http\Controllers'], function ($app) 
{
	// ------------------------------------------------------------------------------------
	// VOUCHERS
	// ------------------------------------------------------------------------------------

	$app->get('/vouchers',
		[
			'uses'				=> 'VoucherController@index'
		]
	);

	$app->get('/voucher/{id}',
		[
			'uses'				=> 'VoucherController@detail'
		]
	);

	$app->post('/voucher/store',
		[
			'uses'				=> 'VoucherController@store'
		]
	);

	// ------------------------------------------------------------------------------------
	// POINTS
	// ------------------------------------------------------------------------------------

	$app->get('/points',
		[
			'uses'				=> 'PointController@index'
		]
	);

	$app->post('/point/store',
		[
			'uses'				=> 'PointController@store'
		]
	);

	// ------------------------------------------------------------------------------------
	// SETTINGS
	// ------------------------------------------------------------------------------------
	
	$app->get('/settings/{type}',
		[
			'uses'				=> 'StoreSettingController@index'
		]
	);

	$app->get('/setting/{id}',
		[
			'uses'				=> 'StoreSettingController@detail'
		]
	);

	$app->post('/setting/store',
		[
			'uses'				=> 'StoreSettingController@store'
		]
	);
});

$app->group(['middleware' => 'oauth|admin', 'namespace' => 'App\Http\Controllers'], function ($app) 
{
	// ------------------------------------------------------------------------------------
	// ADMINISTRATORS
	// ------------------------------------------------------------------------------------

	$app->get('/admins',
		[
			'uses'				=> 'AdminController@index'
		]
	);

	$app->get('/admin/{id}',
		[
			'uses'				=> 'AdminController@detail'
		]
	);

	$app->post('/admin/store',
		[
			'uses'				=> 'AdminController@store'
		]
	);
});