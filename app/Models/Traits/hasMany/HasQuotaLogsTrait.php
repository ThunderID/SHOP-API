<?php namespace App\Models\Traits\hasMany;

/**
 * Trait for models has many QuotaLogs.
 *
 * @author cmooy
 */
trait HasQuotaLogsTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasQuotaLogsTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function QuotaLogs()
	{
		return $this->hasMany('App\Models\QuotaLog');
	}

	/**
	 * check if model has quota log
	 *
	 **/
	public function scopeHasQuotaLogs($query, $variable)
	{
		return $query->whereHas('quotalogs', function($q)use($variable){$q;});
	}

	/**
	 * check if model has quota log in certain id
	 *
	 * @var array or singular id
	 **/
	public function scopeQuotaLogID($query, $variable)
	{
		return $query->whereHas('quotalogs', function($q)use($variable){$q->id($variable);});
	}
}