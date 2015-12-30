<?php namespace App\Models\Traits\belongsToMany;

trait HasTransactionsTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasTransactionsTraitConstructor()
	{
		//
	}

	/* ------------------------------------------------------------------- RELATIONSHIP TO CLUSTER -------------------------------------------------------------------*/

	public function Transactions()
	{
		return $this->belongsToMany('App\Models\Transaction', 'transaction_details');
	}

	public function scopeTransactionsID($query, $variable)
	{
		return $query->whereHas('transactions', function($q)use($variable){$q->id($variable);});
	}
}