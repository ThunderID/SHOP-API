<?php

namespace App\Models;

use App\Models\Traits\HasTypeTrait;
use App\Models\Traits\HasQuotaTrait;
use App\Models\Traits\HasReferencedByTrait;
use App\Models\Traits\HasReferralOfTrait;
use App\Models\Traits\HasTotalPointTrait;
// use App\Models\Observers\UserObserver;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends BaseModel implements AuthenticatableContract, CanResetPasswordContract 
{
    use Authenticatable, CanResetPassword;

	/* ---------------------------------------------------------------------------- RELATIONSHIP TRAITS ---------------------------------------------------------------------*/
	use \App\Models\Traits\hasMany\HasTransactionsTrait;
	use \App\Models\Traits\hasMany\HasPointLogsTrait;
	use \App\Models\Traits\hasMany\HasAuditorsTrait;

	/* ---------------------------------------------------------------------------- GLOBAL SCOPE TRAITS ---------------------------------------------------------------------*/
	use HasQuotaTrait;
	use HasReferencedByTrait;
	use HasTotalPointTrait;
	use HasReferralOfTrait;

	use HasTypeTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'users';

	// protected $timestamps			= true;

	/**
	 * Timestamp field
	 *
	 * @var array
	 */
	protected $dates				=	['created_at', 'updated_at', 'deleted_at', 'joined_at', 'expired_at', 'date_of_birth', 'last_logged_at'];

	/**
	 * The appends attributes from mutator and accessor
	 *
	 * @var array
	 */
	protected $appends				=	[];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden 				= ['password', 'remember_token'];

	/* ---------------------------------------------------------------------------- RELATIONSHIP ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- QUERY BUILDER ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- FUNCTIONS ----------------------------------------------------------------------------*/
	
	public static function boot() 
	{
        parent::boot();
 
        // User::observe(new UserObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
