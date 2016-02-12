<?php

/**
*
* Routes For mail
*
*
* ORDER
*
* Invoice 					: Line 35 
*	
* Paid 						: Line 41
*	
* Shipped 					: Line 47
*	
* Delivered 				: Line 53
*	
* Canceled 					: Line 59
*	
* ACCOUNT
*	
* Reset Password 			: Line 69
*	
* CRM
*	
* Welcome					: Line 79
*/

$app->group(['middleware' => 'oauth', 'namespace' => 'App\Http\Controllers\Mail', 'prefix' => 'mail'], function ($app) 
{
	// ------------------------------------------------------------------------------------
	// ORDER
	// ------------------------------------------------------------------------------------

	$app->post('/invoice',
		[
			'uses'				=> 'OrderController@invoice'
		]
	);

	$app->post('/paid',
		[
			'uses'				=> 'OrderController@paid'
		]
	);

	$app->post('/shipped',
		[
			'uses'				=> 'OrderController@shipped'
		]
	);

	$app->post('/delivered',
		[
			'uses'				=> 'OrderController@delivered'
		]
	);

	$app->post('/canceled',
		[
			'uses'				=> 'OrderController@canceled'
		]
	);

	// ------------------------------------------------------------------------------------
	// ACCOUNT
	// ------------------------------------------------------------------------------------

	$app->post('/password/reset',
		[
			'uses'				=> 'AccountController@password'
		]
	);

	// ------------------------------------------------------------------------------------
	// CRM
	// ------------------------------------------------------------------------------------

	$app->post('/welcome',
		[
			'uses'				=> 'CRMController@welcome'
		]
	);

});