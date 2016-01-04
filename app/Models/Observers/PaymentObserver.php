<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;

use App\Models\ShippingCost;
use App\Models\Transaction;

use App\Jobs\Points\CountShippingCost;

/* ----------------------------------------------------------------------
 * Event:
 * saving
 * deleting
 * ---------------------------------------------------------------------- */

class PaymentObserver 
{
    public function saving($model)
    {
        $errors                             = new MessageBag();

        if($model->transaction()->count())
        {
            $result                         = $this->dispatch(new CheckPaid($model->transaction, $model));
        }

        if(isset($result) && $result->getStatus()=='error')
        {
            $errors->add('Payment', $result->getErrorMessage());
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
        $errors                             = new MessageBag();

        if($model->transaction()->count())
        {
            $result                         = $this->dispatch(new ChangeStatus($model->transaction, 'paid'));
        }

        if(isset($result) && $result->getStatus()=='error')
        {
            $errors->add('Payment', $result->getErrorMessage());
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
        $errors                             = new MessageBag();

        if($model->transaction->count())
        {
            $errors->add('Payment', 'Tidak bisa menghapus data payment yang sudah divalidasi.');
        }

        if($errors->count())
        {
            $model['errors']                = $errors;

            return false;
        }

        return true;
    }
}
