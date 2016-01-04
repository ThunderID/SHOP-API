<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;

use App\Jobs\ChangeStatus;
use App\Jobs\GenerateTransactionRefNumber;

/* ----------------------------------------------------------------------
 * Event:
 * creating
 * created
 * deleting
 * ---------------------------------------------------------------------- */

class PurchaseObserver 
{
	public function creating($model)
	{
		$errors 							= new MessageBag();

        $model->transact_at  				= Carbon::now()->format('Y-m-d H:i:s');

		//generate ref number of transaction
        $result                             = $this->dispatch(new GenerateTransactionRefNumber($model));

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

	public function created($model)
	{
		$errors 							= new MessageBag();

		//change status to delivered
        $result                             = $this->dispatch(new ChangeStatus($model, 'delivered', 'stock'));

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

	public function deleting($model)
    {
		$errors 							= new MessageBag();

    	//1. Remove all details

        foreach ($model->transactiondetails as $key => $value) 
        {
            if(!$value->delete())
            {
            	$errors->add('Log', $value->getError());
            }
        }

    	//2. Remove all logs
        foreach ($model->transactionlogs as $key => $value) 
        {
            if(!$value->delete())
            {
            	$errors->add('Log', $value->getError());
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
