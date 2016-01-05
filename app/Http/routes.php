<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

// ------------------------------------------------------------------------------------
// Authorizer
// ------------------------------------------------------------------------------------

$app->post('/oauth/access_token', function() 
{
	$issue['token']								= \LucaDegasperi\OAuth2Server\Facades\Authorizer::issueAccessToken();

	if(\Illuminate\Support\Facades\Auth::check())
	{
		$issue['me']							= \Illuminate\Support\Facades\Auth::user()->toArray();

		return new \App\Libraries\JSend('success', (array)$issue);
	}
	else
	{
		return new \App\Libraries\JSend('error', (array)['No Data'], 'User invalid :( ');
	}

});

$app->group(['middleware' => 'oauth', 'namespace' => 'App\Http\Controllers'], function ($app) 
{
	// ------------------------------------------------------------------------------------
	// Gettin' Me
	// ------------------------------------------------------------------------------------

	$app->get('/me', function() 
	{
		$user 								= \LucaDegasperi\OAuth2Server\Facades\Authorizer::getResourceOwnerId();

		return new \App\Libraries\JSend('success', (array)$user);
	});

	$app->group(['middleware' => 'oauth|staff', 'namespace' => 'App\Http\Controllers'], function ($app) 
	{
	// ------------------------------------------------------------------------------------
	// PRODUCTS
	// ------------------------------------------------------------------------------------

	//Product it self
	$app->get('/products',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'ProductController@index'
		]
	);

	$app->get('/product/{id}',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'ProductController@detail'
		]
	);

	$app->post('/product/store',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'ProductController@store'
		]
	);

	$app->get('/product/delete/{id}',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'ProductController@delete'
		]
	);

	//Warehouse
	$app->get('/product/stock/card/{id}',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'WarehouseController@card'
		]
	);

	$app->get('/products/stock/critical',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'WarehouseController@critical'
		]
	);

	$app->get('/products/stock/opname',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'WarehouseController@opname'
		]
	);

	// ------------------------------------------------------------------------------------
	// CLUSTERS
	// ------------------------------------------------------------------------------------

	$app->get('/clusters',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'ClusterController@index'
		]
	);

	$app->get('/cluster/{id}',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'ClusterController@detail'
		]
	);

	$app->post('/cluster/store',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'ClusterController@store'
		]
	);

	$app->delete('/cluster/delete/{id}',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'ClusterController@delete'
		]
	);

	// ------------------------------------------------------------------------------------
	// SUPPLIERS
	// ------------------------------------------------------------------------------------

	$app->get('/suppliers',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'SupplierController@index'
		]
	);

	$app->get('/supplier/{id}',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'SupplierController@detail'
		]
	);

	$app->post('/supplier/store',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'SupplierController@store'
		]
	);

	$app->delete('/supplier/delete/{id}',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'SupplierController@delete'
		]
	);

	// ------------------------------------------------------------------------------------
	// PURCHASES
	// ------------------------------------------------------------------------------------

	$app->get('/purchases',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'PurchaseController@index'
		]
	);

	$app->get('/purchase/{id}',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'PurchaseController@detail'
		]
	);

	$app->post('/purchase/store',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'PurchaseController@store'
		]
	);

	// ------------------------------------------------------------------------------------
	// SALES
	// ------------------------------------------------------------------------------------

	$app->get('/sales',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'SaleController@index'
		]
	);

	$app->get('/sale/{id}',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'SaleController@detail'
		]
	);

	$app->post('/sale/update/status',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'SaleController@status'
		]
	);

	// ------------------------------------------------------------------------------------
	// COURIERS
	// ------------------------------------------------------------------------------------

	$app->get('/couriers',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'CourierController@index'
		]
	);

	$app->get('/courier/{id}',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'CourierController@detail'
		]
	);

	$app->post('/courier/store',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'CourierController@store'
		]
	);

	$app->delete('/courier/delete/{id}',
		[
			// 'middleware'		=> 'oauth',
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
			// 'middleware'		=> 'oauth',
			'uses'				=> 'VoucherController@index'
		]
	);

	$app->get('/voucher/{id}',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'VoucherController@detail'
		]
	);

	$app->post('/voucher/store',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'VoucherController@store'
		]
	);

	// ------------------------------------------------------------------------------------
	// POINTS
	// ------------------------------------------------------------------------------------

	$app->get('/points',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'PointController@index'
		]
	);

	$app->post('/point/store',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'PointController@store'
		]
	);

	// ------------------------------------------------------------------------------------
	// SETTING
	// ------------------------------------------------------------------------------------
	$app->get('/settings/{type}',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'StoreSettingController@index'
		]
	);

	$app->get('/setting/{id}',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'StoreSettingController@detail'
		]
	);

	$app->post('/setting/store',
		[
			// 'middleware'		=> 'oauth',
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
			// 'middleware'		=> 'oauth',
			'uses'				=> 'AdminController@index'
		]
	);

	$app->get('/admin/{id}',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'AdminController@detail'
		]
	);

	$app->post('/admin/store',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'AdminController@store'
		]
	);
	});

	// ------------------------------------------------------------------------------------
	// CUSTOMERS
	// ------------------------------------------------------------------------------------

	//authenticate process
	$app->post('/customer/sign/in',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'AuthController@signin'
		]
	);

	$app->post('/customer/sign/up',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'AuthController@signup'
		]
	);

	$app->post('/customer/activate',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'AuthController@activate'
		]
	);

	//backend area
	$app->get('/customers',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'CustomerController@index'
		]
	);

	$app->get('/customer/{id}',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'CustomerController@detail'
		]
	);

	//my area
	$app->get('/me/{id}',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'MyController@detail'
		]
	);

	$app->get('/me/{id}/points',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'MyController@points'
		]
	);

	$app->post('/me/{id}/update',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'MyController@store'
		]
	);

	$app->post('/me/{id}/redeem',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'MyController@redeem'
		]
	);

	//my area for products
	$app->get('/me/{id}/products/recommended',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'MyProductController@recommended'
		]
	);

	$app->get('/me/{id}/products/purchased',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'MyProductController@purchased'
		]
	);

	$app->get('/me/{id}/products/viewed',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'MyProductController@viewed'
		]
	);

	//my area for orders
	$app->get('/me/{id}/orders',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'MyOrderController@index'
		]
	);

	$app->get('/me/{id}/order/{order_id}',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'MyOrderController@detail'
		]
	);

	$app->post('/me/{id}/order/store',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'MyOrderController@store'
		]
	);

	$app->get('/me/{id}/incart',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'MyOrderController@incart'
		]
	);
});
