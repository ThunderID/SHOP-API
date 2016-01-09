<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;

/**
 * Used in Policy model
 *
 * @author cmooy
 */
class PolicyObserver 
{
    /** 
     * observe policy saving
     * 1. act if error or not
     * 
     * @param $model
     * @return bool
     */
	public function saving($model)
    {
        $errors                             = new MessageBag();

        if($errors->count())
        {
            $model['errors']                = $errors;

            return false;
        }

        return true;
    }

    /** 
     * observe policy event deleting
     * 1. refuse delete
     * 
     * @param $model
     * @return bool
     */
    public function deleting($model)
    {
		$model['errors']            = 'Tidak dapat menghapus Pengaturan.';

		return false;

        return true;
    }
}
