<?php namespace App\Models\Traits\belongsToMany;

trait HasClustersTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasClustersTraitConstructor()
	{
		//
	}

	/* ------------------------------------------------------------------- RELATIONSHIP TO CLUSTER -------------------------------------------------------------------*/

	public function Categories()
	{
		return $this->belongsToMany('App\Models\Category', 'categories_products', 'product_id', 'category_id');
	}

	public function scopeCategoriesID($query, $variable)
	{
		return $query->whereHas('categories', function($q)use($variable){$q->id($variable);});
	}

	public function Tags()
	{
		return $this->belongsToMany('App\Models\Tag', 'categories_products', 'product_id', 'category_id');
	}

	public function scopeTagsID($query, $variable)
	{
		return $query->whereHas('tags', function($q)use($variable){$q->id($variable);});
	}
}