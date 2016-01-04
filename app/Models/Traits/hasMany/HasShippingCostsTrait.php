<?php namespace App\Models\Traits\hasMany;

trait HasShippingCostsTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasShippingCostsTraitConstructor()
	{
		//
	}

	/* ------------------------------------------------------------------- RELATIONSHIP TO SERVICE -------------------------------------------------------------------*/

	public function ShippingCosts()
	{
		return $this->hasMany('App\Models\ShippingCost');
	}
}