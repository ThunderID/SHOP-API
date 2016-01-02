<?php namespace App\Models\Traits\hasOne;

trait HasPaymentTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasPaymentTraitConstructor()
	{
		//
	}

	/* ------------------------------------------------------------------- RELATIONSHIP TO SERVICE -------------------------------------------------------------------*/

	public function Payment()
	{
		return $this->hasOne('App\Models\Payment', 'transaction_id');
	}
}