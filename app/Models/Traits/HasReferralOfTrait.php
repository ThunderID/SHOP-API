<?php 

namespace App\Models\Traits;

use App\Models\Scopes\ReferralOfScope;

trait HasReferralOfTrait 
{
    /**
     * Boot the Has ReferralOf trait for a model (used for morph/inherit table).
     *
     * @return void
     */
    public static function bootHasReferralOfTrait()
    {
        static::addGlobalScope(new ReferralOfScope);
    }
}