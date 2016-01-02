<?php namespace App\Models\Traits\hasOne;

trait HasShipmentTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasShipmentTraitConstructor()
	{
		//
	}

	/* ------------------------------------------------------------------- RELATIONSHIP TO SERVICE -------------------------------------------------------------------*/

	public function Shipment()
	{
		return $this->hasOne('App\Models\Shipment', 'transaction_id');
	}
}