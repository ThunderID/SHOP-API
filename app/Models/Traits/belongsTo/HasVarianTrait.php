<?php namespace App\Models\Traits\belongsTo;

trait HasVarianTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasVarianTraitConstructor()
	{
		//
	}

	/* ------------------------------------------------------------------- RELATIONSHIP TO SERVICE -------------------------------------------------------------------*/

	public function Varian()
	{
		return $this->belongsTo('App\Models\Varian');
	}

	public function scopeHasVarian($query, $variable)
	{
		return $query->whereHas('varian', function($q)use($variable){$q;});
	}

	public function scopeVarianID($query, $variable)
	{
		return $query->where('varian_id', $variable);
	}

	public function scopeVarianName($query, $variable)
	{
		return $query->whereHas('varian', function($q)use($variable){$q->name($variable);});
	}
}