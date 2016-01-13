<?php namespace App\Models\Traits\morphTo;

/**
 * Trait for models morph to reference.
 *
 * @author cmooy
 */
trait HasReferenceTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasReferenceTraitConstructor()
	{
		//
	}
	
	/**
	 * call morph to relationship
	 *
	 **/
    public function reference()
    {
        return $this->morphTo();
    }
	
	/**
	 * call reference in particular id
	 *
	 **/
    public function scopeReferenceID($query, $variable)
    {
		return $query->where('reference_id', $variable);
    }
	
	/**
	 * call reference in particular type
	 *
	 **/
    public function scopeReferenceType($query, $variable)
    {
		return $query->where('reference_type', $variable);
    }
}