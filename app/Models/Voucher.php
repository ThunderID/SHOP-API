<?php

/** 
	* Inheritance Campaign Model
	* For every inheritance model, allowed to have only $type, fillable, rules, and available function
*/

namespace App\Models;

// use App\Models\Observers\VoucherObserver;

class Voucher extends Campaign
{
	/**
	 * The public variable that assigned type of inheritance model
	 *
	 * @var string
	 */

	public $type					=	['free_shipping_cost', 'debit_point', 'promo_referral'];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */

	protected $fillable				=	[
											'user_id'						,
											'code'							,
											'type'							,
											'value'							,
											'started_at'					,
											'expired_at'					,
										];

	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'code'							=> 'required|max:255|min:8',
											'type'							=> 'required|max:255',
											'value'							=> 'numeric',
											// 'started_at'					=> 'date_format:"Y-m-d H:i:s"|after:now',
											'expired_at'					=> 'date_format:"Y-m-d H:i:s"|after:now',
										];
	
	/* ---------------------------------------------------------------------------- RELATIONSHIP ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- QUERY BUILDER ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- FUNCTIONS ----------------------------------------------------------------------------*/
		
	public static function boot() 
	{
        parent::boot();
 
        // Voucher::observe(new VoucherObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
