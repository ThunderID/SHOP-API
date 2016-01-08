<?php namespace App\Models\Traits\belongsTo;

/**
 * Trait for models belongs to user.
 *
 * @author cmooy
 */
trait HasUserTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasUserTraitConstructor()
	{
		//
	}

	/**
	 * call belongs to relationship
	 *
	 **/
	public function User()
	{
		return $this->belongsTo('App\Models\User');
	}

	/**
	 * check if model has user
	 *
	 **/
	public function scopeHasUser($query, $variable)
	{
		return $query->whereHas('user', function($q)use($variable){$q;});
	}

	/**
	 * check if model has user in certain id
	 *
	 * @var string id
	 **/
	public function scopeUserID($query, $variable)
	{
		return $query->where('user_id', $variable);
	}

	/**
	 * call belongs to relationship with customer only
	 *
	 **/
	public function Customer()
	{
		return $this->belongsTo('App\Models\Customer', 'user_id');
	}
}