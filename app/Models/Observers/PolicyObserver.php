<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;

use App\Jobs\Auditors\SaveAuditPolicy;

/* ----------------------------------------------------------------------
 * Event:
 * saving
 * deleting
 * ---------------------------------------------------------------------- */

class PolicyObserver 
{
	public function saving($model)
    {
        $errors                             = new MessageBag();

        $result                             = $this->dispatch(new SaveAuditPolicy($model));

        if($result->getStatus()=='error')
        {
            $errors->add('Policy', $result->getErrorMessage());
        }

        if($errors->count())
        {
            $model['errors']                = $errors;

            return false;
        }

        return true;
    }

    public function deleting($model)
    {
		$model['errors']            = 'Tidak dapat menghapus Pengaturan.';

		return false;

        return true;
    }
}
