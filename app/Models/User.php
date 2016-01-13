<?php

namespace App\Models;

use App\Models\Traits\HasTypeTrait;
use App\Models\Traits\HasQuotaTrait;
use App\Models\Traits\HasReferencedByTrait;
use App\Models\Traits\HasReferralOfTrait;
use App\Models\Traits\HasTotalPointTrait;
use App\Models\Observers\UserObserver;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

/**
 * Used for User Models
 * 
 * @author cmooy
 */
class User extends BaseModel implements AuthenticatableContract, CanResetPasswordContract 
{
    use Authenticatable, CanResetPassword;

	/**
	 * Relationship Traits.
	 *
	 */
	use \App\Models\Traits\hasMany\HasTransactionsTrait;
	use \App\Models\Traits\hasMany\HasPointLogsTrait;
	use \App\Models\Traits\hasMany\HasAuditorsTrait;
	use \App\Models\Traits\hasOne\HasReferralTrait;

	/**
	 * Global traits used as query builder (global scope).
	 *
	 */
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

	/**
	 * Timestamp field
	 *
	 * @var array
	 */
	// protected $timestamps			= true;
	
	/**
	 * Date will be returned as carbon
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
	protected $hidden 				= ['password', 'remember_token', 'activation_link', 'reset_password_link', 'expired_at'];

	/* ---------------------------------------------------------------------------- RELATIONSHIP ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- QUERY BUILDER ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- FUNCTIONS ----------------------------------------------------------------------------*/
		
	/**
	 * boot
	 * observing model
	 *
	 */	
	public static function boot() 
	{
        parent::boot();
 
        User::observe(new UserObserver());
    }

	/**
	 * generate referral code
	 * 
	 * @param model of user
	 * @return referral_code
	 */	
    public function generateReferralCode($user)
	{
		$letters 							= 'abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		
		if(!is_null($user->referral_code))
        {
        	return $user->referral_code;
        }
        else
        {
            do
            {
    			$names							= explode(' ', $user->name);
    			$fnames 						= [];
    			$lnames 						= [];
    			$lostcode 						= [];
            	if(isset($names[0]))
            	{
    				$fname 						= str_split($names[0]);

    				foreach ($fname as $key => $value) 
    				{
    					if($key <= 2)
    					{
    						$fnames[$key]		= $value;
    					}
    				}
            	}

            	if(count($fnames) < 3)
            	{
            		foreach (range((count($fnames)-1), 2) as $key) 
            		{
            			$fnames[$key] 			= substr(str_shuffle($letters), 0, 1);
            		}
            	}

            	if(isset($names[count($names)-1]))
            	{
    				$lname 						= str_split($names[count($names)-1]);
    				foreach ($lname as $key => $value) 
    				{
    					if($key <= 2)
    					{
    						$lnames[$key]		= $value;
    					}
    				}
            	}

            	if(count($lnames) < 3)
            	{
            		foreach (range((count($lnames)-1), 2) as $key) 
            		{
            			$lnames[$key] 			= substr(str_shuffle($letters), 0, 1);
            		}
            	}

            	foreach (range(0, 1) as $key) 
        		{
        			$lostcode[$key] 			= substr(str_shuffle($letters), 0, 1);
        		}

        		$lcode 							= implode('', $lnames);
        		$fcode 							= implode('', $fnames);
        		$locode 						= implode('', $lostcode);

    			$referral_code 		            = strtolower($fcode.$lcode.$locode);
 
                $referral                       = User::referralcode($fcode.$lcode.$locode)->first();
            }
            while($referral);

            return $referral_code;
        }
	}

	/**
	 * save referral code
	 * 
	 * @param model of user, referral_code
	 * @return boolean
	 */	
    public function giveReferralCode($user, $referral)
	{
		//save voucher referral
		$newvoucher						= new Referral;
    	$newvoucher->fill([
    		'user_id'					=> $user->id,
			'code'		    			=> $referral,
			'type'						=> 'referral',
			'value'						=> 0,
            'started_at'				=> null,
			'expired_at'				=> null,
    		]);

        if(!$newvoucher->save())
        {
        	$this->errors				= $newvoucher->getError();

        	return false;
        }
        else
        {
        	//save quota referral
        	$quota 						= StoreSetting::type('first_quota')->Ondate('now')->first();

	        if(!$quota)
	        {
	        	$this->errors			= 'Tidak dapat melakukan registrasi saat ini.';
	        	
	        	return false;
	        }
	        else
	        {
	        	$newquota 				= new QuotaLog;
	        	$newquota->fill([
	        		'voucher_id'		=> $newvoucher['id'],
					'amount'			=> $quota->value,
					'notes'				=> 'Hadiah registrasi',
	        		]);

	        	if(!$newquota->save())
	        	{
		        	$this->errors				= $newquota->getError();
        			
        			return false;
	            }
	        }
        }

		return true;
	}

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/

	/**
	 * find email
	 * 
	 * @param email
	 */	
	public function scopeEmail($query, $variable)
	{
		return $query->where('email', $variable);
	}
}
