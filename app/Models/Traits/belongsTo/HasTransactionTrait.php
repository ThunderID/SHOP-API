<?php namespace App\Models\Traits\belongsTo;

/**
 * Trait for models belongs to transaction.
 *
 * @author cmooy
 */
trait HasTransactionTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasTransactionTraitConstructor()
	{
		//
	}

	/**
	 * call belongsto relationship with transaction
	 *
	 **/
	public function Transaction()
	{
		return $this->belongsTo('App\Models\Transaction');
	}
	
	/**
	 * check if model has transaction
	 *
	 **/
	public function scopeHasTransaction($query, $variable)
	{
		return $query->whereHas('transaction', function($q)use($variable){$q;});
	}

	/**
	 * check if model has transaction in certain id
	 *
	 * @var singular id
	 **/
	public function scopeTransactionID($query, $variable)
	{
		return $query->where('transaction_id', $variable);
	}

	/**
	 * check if model has transaction in certain name
	 *
	 * @var singular name
	 **/
	public function scopeTransactionName($query, $variable)
	{
		return $query->whereHas('transaction', function($q)use($variable){$q->name($variable);});
	}
}