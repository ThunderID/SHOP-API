<?php namespace App\Models\Traits\belongsTo;

trait HasUserTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasUserTraitConstructor()
	{
		//
	}

	/* ------------------------------------------------------------------- RELATIONSHIP TO SERVICE -------------------------------------------------------------------*/

	public function User()
	{
		return $this->belongsTo('App\Models\User');
	}

	public function scopeHasUser($query, $variable)
	{
		return $query->whereHas('user', function($q)use($variable){$q;});
	}

	public function scopeUserID($query, $variable)
	{
		return $query->whereHas('user', function($q)use($variable){$q->id($variable);});
	}
}