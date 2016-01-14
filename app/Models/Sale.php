<?php

namespace App\Models;

use App\Models\Traits\HasBillAmountTrait;
use App\Models\Traits\Calculations\HasVoucherQuotaTrait;
use App\Models\Observers\SaleObserver;

/** 
	* Inheritance Transaction Model
	* For every inheritance model, allowed to have only $type, fillable, rules, and available function
*/
class Sale extends Transaction
{
	/**
	 * Relationship Traits.
	 *
	 */
	use \App\Models\Traits\belongsTo\HasUserTrait;
	use \App\Models\Traits\belongsTo\HasVoucherTrait;
	use \App\Models\Traits\hasOne\HasPaymentTrait;
	use \App\Models\Traits\hasOne\HasShipmentTrait;
	use \App\Models\Traits\hasMany\HasPointLogsTrait;
	
	/**
	 * Global traits used as query builder (global scope).
	 *
	 */
	use HasBillAmountTrait;
	use HasVoucherQuotaTrait;

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
	
	/**
	 * boot
	 * observing model
	 *
	 */
	public static function boot() 
	{
        parent::boot();
 
        Sale::observe(new SaleObserver());
    }

	/**
	 * generate unique number
	 * 
	 * @param model of sale
	 * @return unique_number
	 */	
    public function generateUniqueNumber($sale)
	{
		if(!is_null($sale['unique_number']))
        {
            $i                          = 0;
            $amount                     = true;

            while($amount)
            {
                $prev_number            = Sale::orderBy('id', 'DESC')->status('wait')->first();

                $limit                  = StoreSetting::type('limit_unique_number')->ondate('now')->first();

                if($prev_number['unique_number'] < $limit['value'])
                {
                    $unique_number      = $i+ $prev_number['unique_number'] + 1;
                }
                else
                {
                    $unique_number      = $i+ 1;
                }

                $amount                 = Sale::amount($sale->amount - $unique_number)->status('wait')->notid($sale->id)->first();
                $i                      = $i+1;
            }

            return $unique_number;
        }
        else
        {
        	return $sale['unique_number'];
        }
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
