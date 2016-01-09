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
include('routes_private_resource.php');
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
});

