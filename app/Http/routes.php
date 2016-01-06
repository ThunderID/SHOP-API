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

include('routes_authorized.php');
include('routes_protected_resource.php');
// include('routes_private.php');
// include('routes_public.php');

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
			'middleware'		=> 'oauth|admin',
			'uses'				=> 'CustomerController@index'
		]
	);

	$app->get('/customer/{id}',
		[
			'middleware'		=> 'oauth|admin',
			'uses'				=> 'CustomerController@detail'
		]
	);

	$app->group(['middleware' => 'oauth|me', 'namespace' => 'App\Http\Controllers'], function ($app) 
	{
	//my area
	$app->get('/me/{user_id}',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'MyController@detail'
		]
	);

	$app->get('/me/{user_id}/points',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'MyController@points'
		]
	);

	$app->post('/me/{user_id}/update',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'MyController@store'
		]
	);

	$app->post('/me/{user_id}/redeem',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'MyController@redeem'
		]
	);

	//my area for products
	$app->get('/me/{user_id}/products/recommended',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'MyProductController@recommended'
		]
	);

	$app->get('/me/{user_id}/products/purchased',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'MyProductController@purchased'
		]
	);

	$app->get('/me/{user_id}/products/viewed',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'MyProductController@viewed'
		]
	);

	//my area for orders
	$app->get('/me/{user_id}/orders',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'MyOrderController@index'
		]
	);

	$app->get('/me/{user_id}/order/{order_id}',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'MyOrderController@detail'
		]
	);

	$app->get('/me/{user_id}/incart',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'MyOrderController@incart'
		]
	);

	$app->post('/me/{user_id}/order/store',
		[
			// 'middleware'		=> 'oauth',
			'uses'				=> 'MyOrderController@store'
		]
	);
	});
});
