<?php 

namespace App\Models\Traits;

use App\Models\Scopes\ReferencedByScope;

trait HasReferencedByTrait 
{
    /**
     * Boot the Has ReferencedBy trait for a model (used for morph/inherit table).
     *
     * @return void
     */
    public static function bootHasReferencedByTrait()
    {
        static::addGlobalScope(new ReferencedByScope);
    }
}