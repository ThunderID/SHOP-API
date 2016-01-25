<?php namespace App\Models\Traits\hasMany;

/**
 * Trait for models has many Point Logs.
 *
 * @author cmooy
 */
trait HasPointLogsTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasPointLogsTraitConstructor()
	{
		//
	}

	/**
	 * call has many relationship
	 *
	 **/
	public function PointLogs()
	{
		return $this->hasMany('App\Models\PointLog', 'user_id');
	}

	/**
	 * check if model has point log
	 *
	 **/
	public function scopeHasPointLogs($query, $variable)
	{
		return $query->whereHas('pointlogs', function($q)use($variable){$q;});
	}

	/**
	 * check if model has point log in certain id
	 *
	 * @var array or singular id
	 **/
	public function scopePointLogID($query, $variable)
	{
		return $query->whereHas('pointlogs', function($q)use($variable){$q->id($variable);});
	}

	/**
	 * call has many relationship in term of reference were users
	 *
	 **/
	public function MyReferrals()
	{
		return $this->hasMany('App\Models\PointLog', 'reference_id')->where('reference_type', '=', 'App\Models\User');
	}

	/**
	 * call has many relationship in term of used for paid sales
	 *
	 **/
	public function PaidPointLogs()
	{
		return $this->hasMany('App\Models\PointLog', 'reference_id')->where('reference_type', '=', 'App\Models\Transaction')->where('amount', '<', 0);
	}
}