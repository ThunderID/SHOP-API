<?php 

namespace App\Models\Traits;

use App\Models\Scopes\BillAmountScope;

trait HasBillAmountTrait 
{
    /**
     * Boot the Has BillAmount trait for a model (used for morph/inherit table).
     *
     * @return void
     */
    public static function bootHasBillAmountTrait()
    {
        static::addGlobalScope(new BillAmountScope);
    }
}