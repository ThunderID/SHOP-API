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
	 * call has many relationship
	 *
	 **/
	public function Shipments()
	{
		return $this->hasMany('App\Models\Shipment');
	}


	/**
	 * call has many relationship
	 *
	 **/
	public function Shippings()
	{
		return $this->hasMany('App\Models\Shipment')->wherehas('transaction', function($q){$q->status('shipping');});
	}
}
