<?php 

namespace App\Models\Traits;

use App\Models\Scopes\CurrentPriceScope;

trait HasCurrentPriceTrait 
{
    /**
     * Boot the Has CurrentPrice trait for a model (used for morph/inherit table).
     *
     * @return void
     */
    public static function bootHasCurrentPriceTrait()
    {
        static::addGlobalScope(new CurrentPriceScope);
    }
}