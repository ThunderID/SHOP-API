<?php namespace App\Models\Traits;

trait HasNameTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasNameTraitConstructor()
	{
		//
	}


	public function scopeName($query, $variable)
	{
		if(is_array($variable))
		{
			return 	$query->whereIn($query->getModel()->table.'.name', $variable);
		}

		return 	$query->where($query->getModel()->table.'.name', $variable);
	}
}