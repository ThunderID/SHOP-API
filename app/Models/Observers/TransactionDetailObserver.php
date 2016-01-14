<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;

/**
 * Used in TransactionDetail model
 *
 * @author cmooy
 */
class TransactionDetailObserver 
{
    /** 
     * observe transaction detail event saving
     * 1. Check transactions tatus
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
    public function saving($model)
    {
        $errors                             = new MessageBag();

        //1. check transactions tatus
        if($model->transaction()->count() && $model->transaction->status!='cart' && $model->transaction->type=='sell')
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
