<?php namespace App\Models\Traits\hasMany;

trait HasTransactionDetailsTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasTransactionDetailsTraitConstructor()
	{
		//
	}

	/* ------------------------------------------------------------------- RELATIONSHIP TO SERVICE -------------------------------------------------------------------*/

	public function TransactionDetails()
	{
		return $this->hasMany('App\Models\TransactionDetail', 'transaction_id');
	}

	public function scopeHasTransactionDetails($query, $variable)
	{
		return $query->whereHas('transactiondetails', function($q)use($variable){$q;});
	}

	public function scopeTransactionDetailID($query, $variable)
	{
		return $query->whereHas('transactiondetails', function($q)use($variable){$q->id($variable);});
	}
}