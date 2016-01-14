<?php namespace App\Models\Traits;

/**
 * available function to get name of records
 *
 * @author cmooy
 */
trait HasNameTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasNameTraitConstructor()
	{
		//
	}

	/**
	 * scope to get condition where name
	 *
	 * @param string name
	 **/
	public function scopeName($query, $variable)
	{
		// if(is_array($variable))
		// {
		// 	return 	$query->whereIn($query->getModel()->table.'.name', 'like', $variable);
		// }
		return 	$query->where($query->getModel()->table.'.name', 'like', $variable);
	}
}