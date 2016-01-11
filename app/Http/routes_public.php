<?php

/**
*
* Routes For Public Resource
*
*
* Here is where you can register all of the routes for Public resources who can be accessed by everyone from registered engine.
*
* AUTHENTICATE				: Line 22 - 38 
*
* PRODUCTS 					: Line 41 - 45
*/

$app->group(['middleware' => 'oauth', 'namespace' => 'App\Http\Controllers'], function ($app) 
{
	// ------------------------------------------------------------------------------------
	// AUTHENTICATE
	// ------------------------------------------------------------------------------------
	$app->post('/customer/sign/in',
		[
			'uses'				=> 'AuthController@signin'
		]
	);

	$app->post('/customer/sign/up',
		[
			'uses'				=> 'AuthController@signup'
		]
	);

	$app->post('/customer/activate',
		[
			'uses'				=> 'AuthController@activate'
		]
	);

	// ------------------------------------------------------------------------------------
	// PRODUCTS
	// ------------------------------------------------------------------------------------
	$app->get('/products',
		[
			'uses'				=> 'UIController@products'
		]
	);
});
