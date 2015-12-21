<?php 

namespace App\Models\Traits;

use App\Models\Scopes\CurrentStockScope;

trait HasCurrentStockTrait 
{
    /**
     * Boot the Has CurrentStock trait for a model (used for morph/inherit table).
     *
     * @return void
     */
    public static function bootHasCurrentStockTrait()
    {
        static::addGlobalScope(new CurrentStockScope);
    }
}