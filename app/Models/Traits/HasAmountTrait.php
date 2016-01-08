<?php 

namespace App\Models\Traits;

use App\Models\Scopes\AmountScope;

/**
 * Apply scope to get amount of transaction
 *
 * @author cmooy
 */
trait HasAmountTrait 
{
    /**
     * Boot the Has Amount scope for a model to get amount of shop.
     *
     * @return void
     */
    public static function bootHasAmountTrait()
    {
        static::addGlobalScope(new AmountScope);
    }
}