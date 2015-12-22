<?php namespace App\Models\Traits\belongsTo;

trait HasProductTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasProductTraitConstructor()
	{
		//
	}

	/* ------------------------------------------------------------------- RELATIONSHIP TO SERVICE -------------------------------------------------------------------*/

	public function Product()
	{
		return $this->belongsTo('App\Models\Product');
	}

	public function scopeHasProduct($query, $variable)
	{
		return $query->whereHas('product', function($q)use($variable){$q;});
	}

	public function scopeProductID($query, $variable)
	{
		return $query->where('product_id', $variable);
	}

	public function scopeProductName($query, $variable)
	{
		return $query->whereHas('product', function($q)use($variable){$q->name($variable);});
	}
}