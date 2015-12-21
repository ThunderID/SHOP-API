<?php

/** 
	* Inheritance Transaction Model
	* For every inheritance model, allowed to have only $type, fillable, rules, and available function
*/

namespace App\Models;

class Sale extends Transaction
{
	/**
	 * The public variable that assigned type of inheritance model
	 *
	 * @var string
	 */

	public $type					=	'sell';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */

	protected $fillable				=	[
											'user_id'						,
											'voucher_id'					,
											'ref_number'					,
											'type'							,
											'transact_at'					,
											'unique_number'					,
											'shipping_cost'					,
											'voucher_discount'				,
										];
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'ref_number'					=> 'max:255',
											'transact_at'					=> 'date_format:"Y-m-d H:i:s"',
											'unique_number'					=> 'numeric',
											'shipping_cost'					=> 'numeric',
											'voucher_discount'				=> 'numeric',
										];
	
	/* ---------------------------------------------------------------------------- RELATIONSHIP ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- QUERY BUILDER ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- FUNCTIONS ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
