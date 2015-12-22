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

//Product it self
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

$app->get('/cluster/delete/{id}',
	[
		// 'middleware'		=> 'oauth',
		'uses'				=> 'ClusterController@delete'
	]
);