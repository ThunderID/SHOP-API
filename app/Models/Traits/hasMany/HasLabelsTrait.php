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

	public function scopeLabelsName($query, $variable)
	{
		return $query->wherehas('labels', function($q)use($variable){$q->name($variable);});
	}
}