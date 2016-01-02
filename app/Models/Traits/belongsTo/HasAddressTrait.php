<?php namespace App\Models\Traits\belongsTo;

trait HasAddressTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasAddressTraitConstructor()
	{
		//
	}

	/* ------------------------------------------------------------------- RELATIONSHIP TO SERVICE -------------------------------------------------------------------*/

	public function Address()
	{
		return $this->belongsTo('App\Models\Address');
	}
}