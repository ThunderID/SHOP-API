<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;

/* ----------------------------------------------------------------------
 * Event:
 * saving
 * ---------------------------------------------------------------------- */

class TransactionDetailObserver 
{
    public function saving($model)
    {
        $errors                             = new MessageBag();

       if($model->transaction->status!='cart' && $model->transaction->type=='sell')
        {
            $errors->add('Log', 'Tidak dapat menambahkan item baru. Silahkan membuat nota baru.');
        }

        if($errors->count())
        {
            $model['errors']                = $errors;

            return false;
        }

        return true;
    }
}
