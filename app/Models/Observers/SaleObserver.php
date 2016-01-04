<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;

use App\Jobs\ChangeStatus;
use App\Jobs\GenerateTransactionRefNumber;
use App\Jobs\GenerateTransactionUniqNumber;
use App\Jobs\Points\CountVoucherDiscount;
use App\Jobs\Points\CreditQuota;

/* ----------------------------------------------------------------------
 * Event:
 * created
 * saving
 * saved
 * deleting
 * ---------------------------------------------------------------------- */

class SaleObserver 
{
	public function created($model)
	{
		$errors 							= new MessageBag();

		//change status to delivered
        $result                             = $this->dispatch(new ChangeStatus($model, 'cart'));

        if($result->getStatus()=='error')
        {
            $errors->add('Log', $result->getErrorMessage());
        }

		if($errors->count())
        {
            $model['errors']        		= $errors;

            return false;
        }

        return true;
	}

    public function saving($model)
    {
        $errors                             = new MessageBag();

        $result                             = $this->dispatch(new GenerateTransactionRefNumber($model));
        
        if($result->getStatus()=='success')
        {
            if($model->status=='cart' || $model->status=='na')
            {
                $result                     = $this->dispatch(new GenerateTransactionUniqNumber($model));
            }
        
            $result                         = $this->dispatch(new CountVoucherDiscount($model));
        }

        if($result->getStatus()=='error')
        {
            $errors->add('Log', $result->getErrorMessage());
        }

        if($errors->count())
        {
            $model['errors']                = $errors;

            return false;
        }

        return true;
    }

    public function saved($model)
    {
        if($model->voucher()->count())
        {
            $result                             = $this->dispatch(new CreditQuota($model->voucher, 'Penggunaan voucher untuk transaksi #'.$model->ref_number));
        }

        if($result->getStatus()=='error')
        {
            $errors->add('Log', $result->getErrorMessage());
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
        $model['errors']        		= 'Tidak dapat menghapus transaksi, silahkan batalkan.';

        return false;
    }
}
