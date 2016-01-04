<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;

use App\Models\User;

use App\Jobs\Points\AddRefferalCode;
use App\Jobs\Points\AddQuotaRegistration;

/* ----------------------------------------------------------------------
 * Event:
 * creating
 * created
 * deleting
 * ---------------------------------------------------------------------- */

class UserObserver 
{
    public function creating($model)
    {
        $errors                             = new MessageBag();

        if(is_null($model->is_active))
        {
            $model->is_active               = false;
        }

        //activation link used to generate link for first claimed voucher
        if($model->is_active==false)
        {
            $model->activation_link         = md5(uniqid(rand(), TRUE));
        }

        if($errors->count())
        {
            $model['errors']                = $errors;

            return false;
        }

        return true;
    }

    public function created($model)
    {
        $errors                             = new MessageBag();

        //give refferalcode
        $result                             = $this->dispatch(new AddRefferalCode($model));
        
        if($result->getStatus()=='success')
        {
            $voucher                        = json_decode(json_encode($result->getData()), true);
            
            $result                         = $this->dispatch(new AddQuotaRegistration($model, $voucher));
        }

        if(isset($result) && $result->getStatus()=='error')
        {
            $errors->add('User', $result->getErrorMessage());
        }

        if($errors->count())
        {
            $model['errors']                = $errors;

            return false;
        }

        return true;
    }

    public function saving($model)
    {
        $errors                             = new MessageBag();

        if(is_null($model->id))
        {
            $id                             = 0;
        }
        else
        {
            $id                             = $model->id;
        }

        $user                               = User::email($model->email)->notid($id)->first();

        if($user)
        {
            $errors->add('User', 'Email sudah terdaftar.');
        }


        if (Hash::needsRehash($model->password))
        {
            $model->password           = bcrypt($model->password);
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

        if($model->transactions()->count())
        {
            $errors->add('User', 'Tidak bisa menghapus User yang telah bertransaksi.');
        }

        if($model->pointlogs()->count())
        {
            $errors->add('User', 'Tidak bisa menghapus User yang memiliki balin point.');
        }

        if($model->quotalogs()->count())
        {
            $errors->add('User', 'Tidak bisa menghapus User yang memiliki quota.');
        }

        if($model->auditors()->count())
        {
            $errors->add('User', 'Tidak bisa menghapus User yang terlibat dalam sistem audit.');
        }

        if($errors->count())
        {
            $model['errors']                = $errors;

            return false;
        }

        return true;
    }
}
