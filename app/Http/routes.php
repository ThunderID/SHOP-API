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

$app->delete('/product/delete/{id}',
	[
		// 'middleware'		=> 'oauth',
		'uses'				=> 'ProductController@delete'
	]
);

//Product's Stock it self
$app->get('/product/stock/card/{id}',
	[
		// 'middleware'		=> 'oauth',
		'uses'				=> 'StockController@card'
	]
);

$app->get('/products/stock/critical',
	[
		// 'middleware'		=> 'oauth',
		'uses'				=> 'StockController@critical'
	]
);

$app->get('/products/stock/opname',
	[
		// 'middleware'		=> 'oauth',
		'uses'				=> 'StockController@opname'
	]
);