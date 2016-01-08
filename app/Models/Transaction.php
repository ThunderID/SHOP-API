<?php

namespace App\Models;

use Carbon\Carbon;

use App\Models\Traits\HasTypeTrait;
use App\Models\Traits\HasAmountTrait;
use App\Models\Traits\HasStatusTrait;
use App\Models\Traits\HasTransactionStatusTrait;
use App\Models\Observers\TransactionObserver;

/**
 * Used for Sale and Purchase Models
 * 
 * @author cmooy
 */
class Transaction extends BaseModel
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\Models\Traits\hasMany\HasTransactionLogsTrait;
	use \App\Models\Traits\hasMany\HasTransactionDetailsTrait;
	
	/**
	 * Global traits used as query builder (global scope).
	 *
	 */
	use HasAmountTrait;
	use HasCurrentStatusTrait;
	use HasTransactionStatusTrait;

	use HasTypeTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'transactions';

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
	
	/**
	 * boot
	 * observing model
	 *
	 */	
	public static function boot() 
	{
        parent::boot();
 
        Transaction::observe(new TransactionObserver());
    }

	/**
	 * generate ref number
	 * 
	 * @param model of transaction
	 * @return ref number
	 */	
    public generateRefNumber($transaction) 
	{
		if(is_null($transaction->id) || $transaction->ref_number=='0000000000')
        {
            if($transaction->type=='sell' && in_array($transaction->status, ['na', 'cart', 'abandoned']))
            {
                return '0000000000';
            }
            else
            {
                $prefix                         = $transaction->type[0].date("ym");

                $latest_transaction             = Transaction::select('ref_number')
                                                    ->refnumber($prefix)
                                                    ->status(['wait', 'paid', 'packed', 'shipping', 'delivered', 'canceled'])
                                                    ->orderBy('ref_number', 'DESC')
                                                    ->first();

                if(date('Y')=='2015')
                {
                    if(empty($latest_transaction))
                    {
                        $number                     = 47;
                    }
                    else
                    {
                        $number                     = 1 + (int)substr($latest_transaction['ref_number'],6);
                    }
                }
                else
                {
                    if(empty($latest_transaction))
                    {
                        $number                     = 1;
                    }
                    else
                    {
                        $number                     = 1 + (int)substr($latest_transaction['ref_number'],6);
                    }
                }


                return $prefix . str_pad($number,4,"0",STR_PAD_LEFT);
            }
        }
        else
        {
        	return $transaction->ref_number;
        }
    }

	/**
	 * change transaction log (status) of transaction
	 * 
	 * @param model of transaction, status, notes
	 * @return boolean, error message saved to models
	 */	
    public changeStatus($transaction, $status, $notes) 
	{
		$logs 					= new TransactionLog;
		$params 				= 	[
										'transaction_id' 	=> $transaction['id'],
										'status' 			=> $status,
										'notes' 			=> $notes,
										'changed_at'		=> Carbon::now()->format('Y-m-d H:i:s'),
									];

		$logs->fill($params);

		if($logs->save())
		{
			return true;
		}
		else
		{
			$this->errors	= $logs->getError();
			
			return false;
		}
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
