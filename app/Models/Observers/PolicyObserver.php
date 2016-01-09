<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;

/**
 * Used in Product model
 *
 * @author cmooy
 */
class PolicyObserver 
{
    /** 
     * observe policy saving
     * 1. act if error or not
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
     */
    public function deleting($model)
    {
		$model['errors']            = 'Tidak dapat menghapus Pengaturan.';

		return false;

        return true;
    }
}
