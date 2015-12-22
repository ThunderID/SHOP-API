<?php 

namespace App\Models\Traits;

use App\Models\Scopes\AmountScope;

trait HasAmountTrait 
{
    /**
     * Boot the Has Amount trait for a model (used for morph/inherit table).
     *
     * @return void
     */
    public static function bootHasAmountTrait()
    {
        static::addGlobalScope(new AmountScope);
    }
}