<?php 

namespace App\Models\Traits;

use App\Models\Scopes\StatusScope;

trait HasStatusTrait 
{
    /**
     * Boot the Has Status trait for a model (used for morph/inherit table).
     *
     * @return void
     */
    public static function bootHasStatusTrait()
    {
        static::addGlobalScope(new StatusScope);
    }
}