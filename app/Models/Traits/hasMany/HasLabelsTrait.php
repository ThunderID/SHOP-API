<?php namespace App\Models\Traits\hasMany;

trait HasLabelsTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasLabelsTraitConstructor()
	{
		//
	}

	/* ------------------------------------------------------------------- RELATIONSHIP TO SERVICE -------------------------------------------------------------------*/

	public function Labels()
	{
		return $this->hasMany('App\Models\ProductLabel');
	}
}