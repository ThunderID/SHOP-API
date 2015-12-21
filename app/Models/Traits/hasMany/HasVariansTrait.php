<?php namespace App\Models\Traits\hasMany;

trait HasVariansTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasVariansTraitConstructor()
	{
		//
	}

	/* ------------------------------------------------------------------- RELATIONSHIP TO SERVICE -------------------------------------------------------------------*/

	public function Varians()
	{
		return $this->hasMany('App\Models\Varian');
	}
}