<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;

//Points Jobs
use App\Jobs\Points\RevertPoint;
use App\Jobs\Points\CreditPoint;
use App\Jobs\Points\AddQuotaForUpline;
use App\Jobs\Points\AddPointForUpline;

use App\Jobs\ChangeStatus;

/**
 * Used in TransactionLog model
 *
 * @author cmooy
 */
class TransactionLogObserver 
{
    /** 
     * observe transaction log event saving
     * 1. Check if transaction is sale
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function saving($model)
    {
		$errors 						= new MessageBag();

		//A. Check if transaction is sale
        if($model->transaction()->count() && $model->transaction->type=='sell')
        {
            /** 
            * Switch scheme
            * 1. only allowed current status = abandoned if previous was cart
            * 2. only allowed current status = cart if previous was non or cart
            * 3. only allowed current status = wait if whole sale's items were sellable (available stock), and there were shipment address 
            * 4. only allowed current status = paid/packed if bills = 0 
            * 5. only allowed current status = shipping/delivery if receipt number not null 
            * 6. only allowed current status = canceled if bills != 0 
            */
            switch($model->status)
            {
                case 'abandoned' :
                    if($model->transaction->status!='cart')
                    {
                        $errors->add('Log', 'Tidak dapat mengabaikan transaksi yang bukan di keranjang.');
                    }
                break;
                case 'cart' :
                    if($model->transaction->status!='cart' && $model->transaction->status!='na')
                    {
                        $errors->add('Log', 'Tidak dapat mengabaikan transaksi yang sudah checkout.');
                    }
                break;
                case 'wait' :
                    $details                = $model->transaction->transactiondetails;

                    foreach ($details as $key => $value) 
                    {
                        if($value['varian']['current_stock'] < $value['quantity'])
                        {
                            $errors->add('Log', 'Stok '.$value['varian']['product']['name'].' ukuran '.$value['varian']['size']. ' tidak mencukupi');
                        }
                    }

                    if(!$errors->count() && !$model->transaction()->shipment()->address()->count())
                    {
                        $errors->add('Log', 'Tidak dapat checkout tanpa alamat pengiriman.');
                    }
                break;
                case 'paid' : case 'packed' :
                    if(in_array($model->transaction->status, ['cart']))
                    {
                        $errors->add('Log', 'Tidak dapat memvalidasi/packing transaksi yang bukan belum di checkout.');
                    }

                    if($model->transaction->bills!=0)
                    {
                        $errors->add('Log', 'Pembayaran masih kurang sebesar '.$model->transaction->bills.'.');
                    }
                break;
                case 'shipping': case 'delivered' :
                    if($model->transaction->bills!=0)
                    {
                        $errors->add('Log', 'Pembayaran masih kurang sebesar '.$model->transaction->bills.'.');
                    }

                    if(!$model->transaction()->shipment()->count() || is_null($model->transaction->shipment->receipt_number))
                    {
                        $errors->add('Log', 'Tidak dapat checkout tanpa resi pengiriman.');
                    }
                break;
                case 'canceled' :
                    if($model->transaction->bills==0)
                    {
                        $errors->add('Log', 'Tidak dapat membatalkan transaksi yang sudah dibayar.');
                    }
                    elseif($model->transaction->status!='wait')
                    {
                        $errors->add('Log', 'Tidak dapat mengabaikan transaksi yang bukan belum di checkout.');
                    }
                break;
            }
        }
        
        if($errors->count())
        {
			$model['errors'] 		= $errors;

        	return false;
        }

        return true;
    }

    /** 
     * observe transaction log event saved
     * 1. Check if transaction is sale
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
    public function saved($model)
    {
        $errors                                 = new MessageBag();
        
        //A. Check if transaction is sale
        if($model->transaction()->count() && $model->transaction->type=='sell')
        {
           /**
            * Switch scheme
            * 1. current status = cart, save audit abandoned
            * 2. current status = wait, credit point, if full-paid-points,  change status to paid
            * 3. current status = paid, add quota and point for upline, then save audit payment
            * 4. current status = shipping, save audit shipping
            * 5. current status = delivered, save audit delivered
            * 6. current status = canceled, revert point paid, save audit canceled
            */
            switch($model->status)
            {
                case 'cart' :
                break;
                case 'wait' :
                    $result                     = $this->dispatch(new CreditPoint($model->transaction));

                    if($model->transaction->bills==0)
                    {
                        $result                 = $this->ChangeStatus($model->transaction, 'paid');

                        if(!$result)
                        {
                            return false;
                        }
                    }
                break;
                case 'paid' :
                    $result                     = $this->dispatch(new AddQuotaForUpline($model->transaction));
                    if($result->getStatus()=='success')
                    {
                        $result                 = $this->dispatch(new AddPointForUpline($model->transaction));
                    }
                break;
                case 'shipping' :
                break;
                case 'delivered' :
                break;
                case 'canceled' :
                    $result                     = $this->dispatch(new RevertPoint($model->transaction));
                break;
            }

            if(isset($result) && $result->getStatus()=='error')
            {
                $errors->add('Log', $result->getErrorMessage());
            }
        }

        if($errors->count())
        {
            $model['errors']        = $errors;

            return false;
        }

        return true;
    }
    
    public function deleting($model)
    {
		$errors 						= new MessageBag();

        $errors->add('lable', 'Tidak dapat menghapus log transaksi.');

        if($errors->count())
        {
			$model['errors'] 		= $errors;

        	return false;
        }

        return true;
    }
}
