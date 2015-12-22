<?php namespace App\Models\Traits\belongsToMany;

trait HasProductsTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasProductsTraitConstructor()
	{
		//
	}

	/* ------------------------------------------------------------------- RELATIONSHIP TO SERVICE -------------------------------------------------------------------*/

	public function Products()
	{
		return $this->belongsToMany('App\Models\Product', 'categories_products', 'category_id');
	}
}