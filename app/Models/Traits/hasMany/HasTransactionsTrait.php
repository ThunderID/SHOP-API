<?php namespace App\Models\Traits\hasMany;

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

	/* ------------------------------------------------------------------- RELATIONSHIP TO SERVICE -------------------------------------------------------------------*/

	public function Transactions()
	{
		return $this->hasMany('App\Models\Transaction');
	}

	public function scopeHasTransactions($query, $variable)
	{
		return $query->whereHas('transactions', function($q)use($variable){$q;});
	}

	public function scopeTransactionID($query, $variable)
	{
		return $query->whereHas('transactions', function($q)use($variable){$q->id($variable);});
	}
}