<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

//Audit Jobs
use App\Jobs\Auditors\SaveAuditAbandonCart;
use App\Jobs\Auditors\SaveAuditPayment;
use App\Jobs\Auditors\SaveAuditShipment;
use App\Jobs\Auditors\SaveAuditCanceled;
use App\Jobs\Auditors\SaveAuditDelivered;

//Points Jobs
use App\Jobs\Points\RevertPoint;
use App\Jobs\Points\CreditPoint;
use App\Jobs\Points\AddQuotaForUpline;
use App\Jobs\Points\AddPointForUpline;

use App\Jobs\ChangeStatus;

/* ----------------------------------------------------------------------
 * Event:
 * saving
 * saved
 * deleting
 * ---------------------------------------------------------------------- */

class TransactionLogObserver 
{
	public function saving($model)
    {
		$errors 						= new MessageBag();

		//A. Check if transaction is sale
        if($model->transaction()->count() && $model->transaction->type=='sell')
        {
            /*
            Switch scheme
            1. only allowed current status = abandoned if previous was cart
            2. only allowed current status = cart if previous was non or cart
            3. only allowed current status = wait if whole sale's items were sellable (available stock), and there were shipment address 
            4. only allowed current status = paid/packed if bills = 0 
            5. only allowed current status = shipping/delivery if receipt number not null 
            6. only allowed current status = canceled if bills != 0 
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

    public function saved($model)
    {
        $errors                                 = new MessageBag();
        
        //A. Check if transaction is sale
        if($model->transaction()->count() && $model->transaction->type=='sell')
        {
           /*
            Switch scheme
            1. current status = cart, save audit abandoned
            2. current status = wait, credit point, if full-paid-points,  change status to paid
            3. current status = paid, add quota and point for upline, then save audit payment
            4. current status = shipping, save audit shipping
            5. current status = delivered, save audit delivered
            6. current status = canceled, revert point paid, save audit canceled
            */
            switch($model->status)
            {
                case 'cart' :
                    $result                     = $this->dispatch(new SaveAuditAbandonCart($model->transaction));
                break;
                case 'wait' :
                    $result                     = $this->dispatch(new CreditPoint($model->transaction));

                    if($model->transaction->bills==0)
                    {
                        $result                 = $this->dispatch(new ChangeStatus($model->transaction, 'paid'));
                    }
                break;
                case 'paid' :
                    $result                     = $this->dispatch(new AddQuotaForUpline($model->transaction));
                    if($result->getStatus()=='success')
                    {
                        $result                 = $this->dispatch(new AddPointForUpline($model->transaction));
                    }
                    
                    if($result->getStatus()=='success')
                    {
                        $result                 = $this->dispatch(new SaveAuditPayment($model->transaction));
                    }
                break;
                case 'shipping' :
                    $result                     = $this->dispatch(new SaveAuditShipment($model->transaction));
                break;
                case 'delivered' :
                    $result                     = $this->dispatch(new SaveAuditDelivered($model->transaction));
                break;
                case 'canceled' :
                    $result                     = $this->dispatch(new RevertPoint($model->transaction));
                    if($result->getStatus()=='success')
                    {
                        $result                 = $this->dispatch(new SaveAuditCanceled($model->transaction));
                    }
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
