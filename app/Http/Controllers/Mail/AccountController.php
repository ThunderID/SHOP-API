<?php

namespace App\Http\Controllers\Mail;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Input;

use Illuminate\Support\Facades\Mail;

use App\Libraries\JSend;

use \Exception;

/**
 * Handle order mail sender
 * 
 * @author cmooy
 */
class AccountController extends Controller
{
	function __construct()
	{
		$this->clientId		= \LucaDegasperi\OAuth2Server\Facades\Authorizer::getClientId();

		$template 			= \App\Models\ClientTemplate::clientid($this->clientId)->first();

		if(!$template)
		{
			\App::abort(404);
		}

		$this->template 	= $template['located'];
	}

	/**
	 * Send balin reset password
	 *
	 * @param user, store
	 * @return JSend Response
	 */
	public function password()
	{
		$user 					= Input::get('user');
		$store 					= Input::get('store');

		// checking user data
		if(empty($user))
		{
			throw new Exception('Sent variable must be array of a record.');
		}

		// checking store data
		if(empty($store))
		{
			throw new Exception('Sent variable must be array of a record.');
		}

		$data						= ['user' => $user, 'balin' => $store];

		//send mail
		Mail::send('mail.'.$this->template.'.account.password', ['data' => $data], function($message) use($user)
		{
			$message->to($user['email'], $user['name'])->subject(strtoupper($this->template).' - RESET PASSWORD');
		}); 
		
		return new JSend('success', (array)Input::all());
	}
}