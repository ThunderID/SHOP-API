<?php namespace App\Models\Observers;

/* ----------------------------------------------------------------------
 * Event:
 * deleting
 * ---------------------------------------------------------------------- */

class StoreSettingObserver 
{
    public function deleting($model)
    {
		$model['errors']            = 'Tidak dapat menghapus Pengaturan.';

		return false;

        return true;
    }
}
