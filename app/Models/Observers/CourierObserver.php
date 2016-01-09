<?php namespace App\Models\Observers;

/**
 * Used in Courier model
 *
 * @author cmooy
 */
class CourierObserver 
{
	/** 
     * observe courier event deleting
     * 1. check courier relationship
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
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
