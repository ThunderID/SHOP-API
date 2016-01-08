<?php namespace App\Models\Traits\morphMany;

/**
 * Trait for models has many Labels.
 *
 * @author cmooy
 */
trait HasImagesTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasImagesTraitConstructor()
	{
		//
	}
	
	/**
	 * call has many relationship
	 *
	 **/
	public function Images()
	{
		return $this->morphMany('App\Models\Image', 'imageable')->orderby('created_at','desc');
	}
}