<?php namespace App\Models\Traits\belongsTo;

trait HasTransactionTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasTransactionTraitConstructor()
	{
		//
	}

	/* ------------------------------------------------------------------- RELATIONSHIP TO SERVICE -------------------------------------------------------------------*/

	public function Transaction()
	{
		return $this->belongsTo('App\Models\Transaction');
	}

	public function Purchase()
	{
		return $this->belongsTo('App\Models\Purchase');
	}

	public function scopeHasTransaction($query, $variable)
	{
		return $query->whereHas('transaction', function($q)use($variable){$q;});
	}

	public function scopeTransactionID($query, $variable)
	{
		return $query->where('transaction_id', $variable);
	}

	public function scopeTransactionName($query, $variable)
	{
		return $query->whereHas('transaction', function($q)use($variable){$q->name($variable);});
	}
}