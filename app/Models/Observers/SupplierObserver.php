<?php namespace App\Models\Observers;

/* ----------------------------------------------------------------------
 * Event:
 * deleting
 * ---------------------------------------------------------------------- */

class SupplierObserver 
{
    public function deleting($model)
    {
        if($model->transactions()->count())
        {
            $model['errors']            = 'Tidak dapat menghapus supplier yang pernah melakukan transaksi.';

            return false;
        }

        return true;
    }
}
