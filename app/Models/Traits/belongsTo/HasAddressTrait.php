<?php namespace App\Models\Traits\belongsTo;

/**
 * Trait for models belongs to address.
 *
 * @author cmooy
 */
trait HasAddressTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasAddressTraitConstructor()
	{
		//
	}
	
	/**
	 * check if model has address
	 *
	 **/
	public function Address()
	{
		return $this->belongsTo('App\Models\Address');
	}
}