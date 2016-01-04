<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;

use App\Jobs\Auditors\SaveAuditVoucher;

/* ----------------------------------------------------------------------
 * Event:
 * saving
 * saved
 * ---------------------------------------------------------------------- */

class VoucherObserver 
{
	public function saving($model)
	{
		$errors 							= new MessageBag();

        if($model->transactions->count())
        {
            $errors->add('Voucher', 'Tidak dapat mengubah voucher yang telah digunakan dalam transaksi.');
        }

		if($errors->count())
        {
            $model['errors']        		= $errors;

            return false;
        }

        return true;
	}

	public function saved($model)
	{
		$errors 							= new MessageBag();

		//store audit
        $result                             = $this->dispatch(new SaveAuditVoucher($model));

        if($result->getStatus()=='error')
        {
            $errors->add('Voucher', $result->getErrorMessage());
        }

		if($errors->count())
        {
            $model['errors']        		= $errors;

            return false;
        }

        return true;
	}

	public function deleting($model)
    {
		$errors 							= new MessageBag();

    	//1. Check transaction used this voucher

        if($model->transactions()->count())
        {
            $errors->add('Voucher', 'Tidak dapat menghapus voucher yang telah digunakan dalam transaksi.');
        }

        foreach ($model->quotalogs as $key => $value) 
        {
            if(!$value->delete())
            {
                $errors->add('Voucher', $value->getError());
            }
        }

        if($errors->count())
        {
            $model['errors']        		= $errors;

            return false;
        }

        return true;
    }
}
