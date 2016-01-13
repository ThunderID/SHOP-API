<?php namespace App\Models\Traits\hasMany;

/**
 * Trait for models has one shipment.
 *
 * @author cmooy
 */
trait HasShipmentsTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasShipmentsTraitConstructor()
	{
		//
	}

	/**
	 * call has one relationship
	 *
	 **/
	public function Shipments()
	{
		return $this->hasMany('App\Models\Shipment');
	}
}
