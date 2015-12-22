<?php namespace App\Models\Traits\hasMany;

use DB;

trait HasPricesTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasPricesTraitConstructor()
	{
		//
	}

	/* ------------------------------------------------------------------- RELATIONSHIP TO SERVICE -------------------------------------------------------------------*/

	public function Prices()
	{
		return $this->hasMany('App\Models\Price');
	}

	public function scopeHasPrices($query, $variable)
	{
		return $query->whereHas('prices', function($q)use($variable){$q;});
	}

	public function scopePriceID($query, $variable)
	{
		return $query->whereHas('prices', function($q)use($variable){$q->id($variable);});
	}
}