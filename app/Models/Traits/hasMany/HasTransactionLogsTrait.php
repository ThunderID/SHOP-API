<?php namespace App\Models\Traits\hasMany;

use DB;

/**
 * Trait for models has many TransactionLogs.
 *
 * @author cmooy
 */
trait HasTransactionLogsTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasTransactionLogsTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function TransactionLogs()
	{
		return $this->hasMany('App\Models\TransactionLog', 'transaction_id');
	}

	/**
	 * call has many relationship in orderlogs where status in wait, paid, packing, shipping, delivered
	 *
	 **/
	public function OrderLogs()
	{
		return $this->hasMany('App\Models\TransactionLog', 'transaction_id')->wherein('status', ['wait', 'paid', 'packing', 'shipping', 'delivered', 'canceled']);
	}

	/**
	 * check if model has transaction logs
	 *
	 **/
	public function scopeHasTransactionLogs($query, $variable)
	{
		return $query->whereHas('transactionlogs', function($q)use($variable){$q;});
	}

	/**
	 * check if model has transaction logs in certain id
	 *
	 * @var array or singular id
	 **/
	public function scopeTransactionLogID($query, $variable)
	{
		return $query->whereHas('transactionlogs', function($q)use($variable){$q->id($variable);});
	}

	/**
	 * find status in transaction logs statuses
	 *
	 * @var string of status
	 **/
	public function scopeStatus($query, $variable)
	{
		if(is_array($variable))
		{
			return 	$query->whereIn('status', $variable);
		}

		return 	$query->where('status', $variable);
	}
}