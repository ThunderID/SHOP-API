<?php namespace App\Models\Traits;

/**
 * Trait for models has many scopeStats.
 *
 * @author cmooy
 */
trait HasStatableTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasStatableTraitConstructor()
	{
		//
	}
	
	/**
	 * call has many relationship
	 *
	 **/
	public function scopeStats($query, $variable)
	{
		return $query->selectraw('IFNULL(SUM(view), 0) as views')
			->leftjoin('stat_user_views', function ($join) use($variable) 
			{
				$join->on ( 'products.id', '=', 'stat_user_views.statable_id' )
					->where('statable_type', '=', get_class($this))
					->where('statable_id', '=', $variable)
					->where('ondate', '=', date('Y-m-d H:i:s'))
					->wherenull('stat_user_views.deleted_at')
					;
			})
			->orderby('views', 'desc')
		;
	}
}