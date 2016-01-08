<?php


namespace App\Models;

use App\Models\Observers\PurchaseObserver;

/** 
	* Inheritance Transaction Model
	* For every inheritance model, allowed to have only $type, fillable, rules, and available function
*/
class Purchase extends Transaction
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\Models\Traits\belongsTo\HasSupplierTrait;
	
	/**
	 * The public variable that assigned type of inheritance model
	 *
	 * @var string
	 */
	public $type					=	'buy';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable				=	[
											'supplier_id'					,
											'ref_number'					,
											'transact_at'					,
										];

	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'ref_number'					=> 'max:255',
											'transact_at'					=> 'date_format:"Y-m-d H:i:s"',
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
 
        Purchase::observe(new PurchaseObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
