<?php 

namespace App\Models\Traits;

use App\Models\Scopes\QuotaScope;

trait HasQuotaTrait 
{
    /**
     * Boot the Has Quota trait for a model (used for morph/inherit table).
     *
     * @return void
     */
    public static function bootHasQuotaTrait()
    {
        static::addGlobalScope(new QuotaScope);
    }
}