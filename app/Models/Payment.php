<?php

namespace App\Models;

use App\Models\Observers\PaymentObserver;
use App\Models\Traits\Calculations\HasPaidTrait;
use App\Models\Traits\Changes\HasStatusLogTrait;

/**
 * Used for Payment Models
 * 
 * @author cmooy
 */
class Payment extends BaseModel
{
	/**
	 * Global traits used as query builder (global scope).
	 *
	 */
	use HasPaidTrait;
	use HasStatusLogTrait;
	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'payments';

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
	protected $dates				=	['created_at', 'updated_at', 'deleted_at', 'ondate'];

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
	protected $hidden 				= [];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */

	protected $fillable				=	[
											'transaction_id'				,
											'method'						,
											'destination'					,
											'account_name'					,
											'account_number'				,
											'ondate'						,
											'amount'						,
										];

	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'method'						=> 'required|max:255',
											'destination'					=> 'required|max:255',
											'account_name'					=> 'required|max:255',
											'account_number'				=> 'required|max:255',
											'ondate'						=> 'required|date_format:"Y-m-d H:i:s"',
											'amount'						=> 'required|numeric',
										];
	
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
 
        Payment::observe(new PaymentObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
