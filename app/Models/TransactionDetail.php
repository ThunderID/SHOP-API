<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

use App\Models\Traits\HasStockTrait;
use App\Models\Traits\HasTransactionStatusTrait;
// use App\Models\Observers\TransactionDetailObserver;

class TransactionDetail extends BaseModel
{
	/* ---------------------------------------------------------------------------- RELATIONSHIP TRAITS ---------------------------------------------------------------------*/
	use \App\Models\Traits\belongsTo\HasVarianTrait;

	/* ---------------------------------------------------------------------------- GLOBAL PLUG SCOPE TRAITS ---------------------------------------------------------------------*/
	use HasStockTrait;
	use HasTransactionStatusTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'transaction_details';

	// protected $timestamps			= true;

	/**
	 * Timestamp field
	 *
	 * @var array
	 */
	protected $dates				=	['created_at', 'updated_at', 'deleted_at'];

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
											'varian_id'						,
											'quantity'						,
											'price'							,
											'discount'						,
										];

	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'quantity'						=> 'required|numeric',
											'price'							=> 'required|numeric',
											'discount'						=> 'required|numeric',
										];
	
	/* ---------------------------------------------------------------------------- RELATIONSHIP ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- QUERY BUILDER ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- FUNCTIONS ----------------------------------------------------------------------------*/
		
	public static function boot() 
	{
        parent::boot();
 
        // TransactionDetail::observe(new TransactionDetailObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
	
	public function scopeStockMovement($query, $variable)
	{
		return 	$query
					->selectraw('transaction_details.varian_id')
					->selectraw('transactions.transact_at')
					->selectraw('sum(if(transactions.type = "buy", quantity, 0)) as stock_in')
					->selectraw('sum(if(transactions.type = "sell", quantity, 0)) as stock_out')
					->TransactionStockOn(['wait', 'paid', 'packed', 'shipping', 'delivered'])
					->groupby('transactions.id')
					->orderByRaw(DB::raw('varian_id asc, transactions.transact_at asc'))
					;
		;
	}

	public function scopeCritical($query, $variable)
	{
		return 	$query
				->selectraw('transaction_details.*')
				// ->selectcurrentstock(true)
				->TransactionStockOn(['wait', 'paid', 'packed', 'shipping', 'delivered'])
				->HavingCurrentStock($variable)
				// ->orderby('current_stock', 'asc')
				->groupBy('varian_id')
				;
	}

	public function scopeGlobalStock($query, $variable)
	{
		return 	$query
					->selectraw('transaction_detsls.*')
					->selectglobalstock(true)
					->LeftTransactionStockOn(['wait', 'paid', 'packed', 'shipping', 'delivered'])
					->groupBy('varian_id')
					;
		;
	}
}
