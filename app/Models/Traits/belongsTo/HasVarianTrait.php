<?php namespace App\Models\Traits\belongsTo;

/**
 * Trait for models belongs to varian.
 *
 * @author cmooy
 */
trait HasVarianTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasVarianTraitConstructor()
	{
		//
	}


	/**
	 * call belongsto relationship with varian
	 *
	 **/
	public function Varian()
	{
		return $this->belongsTo('App\Models\Varian');
	}

	/**
	 * check if model has varian
	 *
	 **/
	public function scopeHasVarian($query, $variable)
	{
		return $query->whereHas('varian', function($q)use($variable){$q;});
	}

	/**
	 * check if model has varian in certain id
	 *
	 * @var singular id
	 **/
	public function scopeVarianID($query, $variable)
	{
		return $query->where('varian_id', $variable);
	}

	/**
	 * check if model has varian in certain name
	 *
	 * @var singular name
	 **/
	public function scopeVarianName($query, $variable)
	{
		return $query->whereHas('varian', function($q)use($variable){$q->name($variable);});
	}
}