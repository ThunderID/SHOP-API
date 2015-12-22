<?php

namespace App\Models;

use App\Models\Traits\HasTypeTrait;
use App\Models\Traits\HasAmountTrait;
use App\Models\Traits\HasStatusTrait;
use App\Models\Traits\HasTransactionStatusTrait;
use App\Models\Observers\TransactionObserver;

class Transaction extends BaseModel
{
	/* ---------------------------------------------------------------------------- RELATIONSHIP TRAITS ---------------------------------------------------------------------*/
	use \App\Models\Traits\hasMany\HasTransactionLogsTrait;
	use \App\Models\Traits\hasMany\HasTransactionDetailsTrait;
	
	/* ---------------------------------------------------------------------------- GLOBAL SCOPE TRAITS ---------------------------------------------------------------------*/
	use HasAmountTrait;
	use HasStatusTrait;
	use HasTransactionStatusTrait;

	use HasTypeTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'transactions';

	// protected $timestamps			= true;

	/**
	 * Timestamp field
	 *
	 * @var array
	 */
	protected $dates				=	['created_at', 'updated_at', 'deleted_at', 'transact_at'];

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

	/* ---------------------------------------------------------------------------- RELATIONSHIP ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- QUERY BUILDER ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- FUNCTIONS ----------------------------------------------------------------------------*/
	
	public static function boot() 
	{
        parent::boot();
 
        Transaction::observe(new TransactionObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
