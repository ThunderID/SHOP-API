<?php namespace App\Models\Traits\morphMany;

trait HasImagesTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasImagesTraitConstructor()
	{
		//
	}

	public function Images()
	{
		return $this->morphMany('App\Models\Image', 'imageable')->orderby('created_at','desc');
	}
}