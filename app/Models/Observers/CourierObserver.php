<?php namespace App\Models\Observers;

/* ----------------------------------------------------------------------
 * Event:
 * deleting
 * ---------------------------------------------------------------------- */

class CourierObserver 
{
    public function deleting($model)
    {
        if($this->courier->shipments()->count() || $this->courier->shippingcosts()->count())
        {
            $model['errors']            = 'Tidak dapat menghapus Kurir yang pernah melakukan transaksi.';

            return false;
        }

        return true;
    }
}
