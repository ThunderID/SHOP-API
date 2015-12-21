<?php 

namespace App\Models\Traits;

use App\Models\Scopes\DefaultImageScope;

trait HasDefaultImageTrait 
{
    /**
     * Boot the Has DefaultImage trait for a model (used for morph/inherit table).
     *
     * @return void
     */
    public static function bootHasDefaultImageTrait()
    {
        static::addGlobalScope(new DefaultImageScope);
    }
}