<?php namespace App\Models\Traits\belongsToMany;

/**
 * Trait for models belongs to many Clusters.
 *
 * @author cmooy
 */
trait HasClustersTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasClustersTraitConstructor()
	{
		//
	}

	/**
	 * call belongsto many relationship category's type
	 *
	 **/
	public function Categories()
	{
		return $this->belongsToMany('App\Models\Category', 'categories_products', 'product_id', 'category_id');
	}

	/**
	 * check if model has category in certain id
	 *
	 * @var array or singular id
	 **/
	public function scopeCategoriesID($query, $variable)
	{
		return $query->whereHas('categories', function($q)use($variable){$q->id($variable);});
	}

	/**
	 * call belongsto many relationship tag's type
	 *
	 **/
	public function Tags()
	{
		return $this->belongsToMany('App\Models\Tag', 'categories_products', 'product_id', 'category_id');
	}

	/**
	 * check if model has tag in certain id
	 *
	 * @var array or singular id
	 **/
	public function scopeTagsID($query, $variable)
	{
		return $query->whereHas('tags', function($q)use($variable){$q->id($variable);});
	}
}