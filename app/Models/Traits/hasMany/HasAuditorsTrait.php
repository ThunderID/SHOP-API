<?php namespace App\Models\Traits\hasMany;

trait HasAuditorsTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasAuditorsTraitConstructor()
	{
		//
	}

	/* ------------------------------------------------------------------- RELATIONSHIP TO SERVICE -------------------------------------------------------------------*/

	public function Audits()
	{
		return $this->hasMany('App\Models\Auditor', 'user_id');
	}

	public function scopeHasAuditors($query, $variable)
	{
		return $query->whereHas('pointlogs', function($q)use($variable){$q;});
	}

	public function scopeAuditorID($query, $variable)
	{
		return $query->whereHas('pointlogs', function($q)use($variable){$q->id($variable);});
	}
}