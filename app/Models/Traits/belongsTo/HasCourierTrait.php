<?php namespace App\Models\Traits\belongsTo;

trait HasCourierTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasCourierTraitConstructor()
	{
		//
	}

	/* ------------------------------------------------------------------- RELATIONSHIP TO SERVICE -------------------------------------------------------------------*/

	public function Courier()
	{
		return $this->belongsTo('App\Models\Courier');
	}

	public function scopeHasCourier($query, $variable)
	{
		return $query->whereHas('courier', function($q)use($variable){$q;});
	}

	public function scopeCourierID($query, $variable)
	{
		return $query->where('courier_id', $variable);
	}

	public function scopeCourierName($query, $variable)
	{
		return $query->whereHas('courier', function($q)use($variable){$q->name($variable);});
	}
}