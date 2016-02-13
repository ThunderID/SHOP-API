<?php

/**
*
* Routes For mail
*
*
* ORDER
*
* Invoice 					: Line 37 
*	
* Paid 						: Line 43
*	
* Shipped 					: Line 49
*	
* Delivered 				: Line 55
*	
* Canceled 					: Line 61
*	
* ACCOUNT
*	
* Reset Password 			: Line 71
*	
* Send Invitation 			: Line 77
*	
* CRM
*	
* Welcome					: Line 87
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

	$app->post('/invitation',
		[
			'uses'				=> 'AccountController@invitation'
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