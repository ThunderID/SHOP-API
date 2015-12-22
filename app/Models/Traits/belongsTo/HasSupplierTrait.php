<?php namespace App\Models\Traits\belongsTo;

trait HasSupplierTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasSupplierTraitConstructor()
	{
		//
	}

	/* ------------------------------------------------------------------- RELATIONSHIP TO SERVICE -------------------------------------------------------------------*/

	public function Supplier()
	{
		return $this->belongsTo('App\Models\Supplier');
	}

	public function scopeHasSupplier($query, $variable)
	{
		return $query->whereHas('supplier', function($q)use($variable){$q;});
	}

	public function scopeSupplierID($query, $variable)
	{
		return $query->whereHas('supplier', function($q)use($variable){$q->id($variable);});
	}
}