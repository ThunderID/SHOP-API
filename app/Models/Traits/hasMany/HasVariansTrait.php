<?php namespace App\Models\Traits\hasMany;

/**
 * Trait for models has many varians.
 *
 * @author cmooy
 */

trait HasVariansTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasVariansTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function Varians()
	{
		return $this->hasMany('App\Models\Varian');
	}
}