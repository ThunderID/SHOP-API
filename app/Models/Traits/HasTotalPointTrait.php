<?php 

namespace App\Models\Traits;

use App\Models\Scopes\TotalPointScope;

trait HasTotalPointTrait 
{
    /**
     * Boot the Has TotalPoint trait for a model (used for morph/inherit table).
     *
     * @return void
     */
    public static function bootHasTotalPointTrait()
    {
        static::addGlobalScope(new TotalPointScope);
    }
}