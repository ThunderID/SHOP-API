<?php namespace App\Models\Traits\hasMany;

trait HasQuotaLogsTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasQuotaLogsTraitConstructor()
	{
		//
	}

	/* ------------------------------------------------------------------- RELATIONSHIP TO SERVICE -------------------------------------------------------------------*/

	public function QuotaLogs()
	{
		return $this->hasMany('App\Models\QuotaLog');
	}

	public function scopeHasQuotaLogs($query, $variable)
	{
		return $query->whereHas('quotalogs', function($q)use($variable){$q;});
	}

	public function scopeQuotaLogID($query, $variable)
	{
		return $query->whereHas('quotalogs', function($q)use($variable){$q->id($variable);});
	}
}