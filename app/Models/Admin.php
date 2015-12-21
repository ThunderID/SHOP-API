<?php

/** 
	* Inheritance User Model
	* For every inheritance model, allowed to have only $type, fillable, rules, and available function
*/

namespace App\Models;

// use App\Models\Observers\AdminObserver;

class Admin extends User
{
	/**
	 * The public variable that assigned type of inheritance model
	 *
	 * @var string
	 */

	public $type_field				=	'role';

	public $type					=	['staff', 'store_manager', 'admin'];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */

	protected $fillable				=	[
											'name'							,
											'email'							,
											'password'						,
											'role'							,
											'is_active'						,
											'sso_id'						,
											'sso_media'						,
											'sso_data'						,
											'gender'						,
											'date_of_birth'					,
											'activation_link'				,
											'reset_password_link'			,
											'expired_at'					,
											'last_logged_at'				,
										];

	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'name'							=> 'required|max:255',
											'email'							=> 'max:255|email',
											'date_of_birth'					=> 'date_format:"Y-m-d H:i:s"|before:13 years ago'
										];
	
	/* ---------------------------------------------------------------------------- RELATIONSHIP ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- QUERY BUILDER ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- FUNCTIONS ----------------------------------------------------------------------------*/
		
	public static function boot() 
	{
        parent::boot();
 
        // Admin::observe(new AdminObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
