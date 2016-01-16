<?php

/**
*
* Routes For Public Resource
*
*
* Here is where you can register all of the routes for Public resources who can be accessed by everyone from registered engine.
*
* AUTHENTICATE				: Line 24 - 40 
*
* PRODUCTS 					: Line 45 - 49
*
* CLUSTERS 					: Line 54 - 58
*
* LABELS 					: Line 63 - 67
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
	$app->get('/balin/store/products',
		[
			'uses'				=> 'UIController@products'
		]
	);

	// ------------------------------------------------------------------------------------
	// CLUSTERS
	// ------------------------------------------------------------------------------------
	$app->get('/balin/store/clusters/{type}',
		[
			'uses'				=> 'UIController@clusters'
		]
	);

	// ------------------------------------------------------------------------------------
	// CLUSTERS
	// ------------------------------------------------------------------------------------
	$app->get('/balin/store/labels',
		[
			'uses'				=> 'UIController@labels'
		]
	);
});
