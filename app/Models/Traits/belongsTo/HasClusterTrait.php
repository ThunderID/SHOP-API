<?php namespace App\Models\Traits\belongsTo;

trait HasClusterTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasClusterTraitConstructor()
	{
		//
	}

	/* ------------------------------------------------------------------- RELATIONSHIP TO SERVICE -------------------------------------------------------------------*/

	public function Cluster()
	{
		return $this->belongsTo('App\Models\Cluster', 'category_id');
	}

	public function scopeHasCluster($query, $variable)
	{
		return $query->whereHas('cluster', function($q)use($variable){$q;});
	}

	public function scopeClusterID($query, $variable)
	{
		return $query->whereHas('cluster', function($q)use($variable){$q->id($variable);});
	}

	public function Category()
	{
		return $this->belongsTo('App\Models\Category', 'category_id');
	}

	public function Tag()
	{
		return $this->belongsTo('App\Models\Tag', 'category_id');
	}
}