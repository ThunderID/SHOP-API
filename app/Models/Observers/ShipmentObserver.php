<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;

use App\Models\ShippingCost;
use App\Models\Transaction;

use App\Jobs\Points\CountShippingCost;

/* ----------------------------------------------------------------------
 * Event:
 * saving
 * updated
 * deleting
 * ---------------------------------------------------------------------- */

class ShipmentObserver 
{
    public function saving($model)
    {
        $errors                             = new MessageBag();

        //recalculate shipping_cost
        $shippingcost                       = ShippingCost::courierid($model->courier_id)->postalcode($model->address->zipcode)->first();

        if($shippingcost)
        {
            $transaction                    = Transaction::findorfail($model->transaction_id);

            $result                         = $this->dispatch(new CountShippingCost($transaction->transactiondetails, $shippingcost['cost']));

            if($result->getStatus()=='success')
            {
                $transaction->fill(['shipping_cost' => $result->getData()['shipping_cost']]);

                if(!$transaction->save())
                {
                    $errors->add('Shipment', $transaction->getError());
                }
            }
            else
            {
                $errors->add('Shipment', $result->getErrorMessage());
            }
        }
        else
        {
            $errors->add('Shipment', 'Tidak ada kurir ke tempat anda (Silahkan periksa kembali kode pos anda).');
        }

        if($errors->count())
        {
            $model['errors']                = $errors;

            return false;
        }

        return true;
    }

    public function updated($model)
    {
        $errors                             = new MessageBag();

        if(!is_null($model->receipt_number))
        {
            $result                         = $this->dispatch(new ChangeStatus($model->transaction, 'shipping'));
        }

        if(isset($result) && $result->getStatus()=='error')
        {
            $errors->add('Shipment', $result->getErrorMessage());
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

        if(!is_null($model->receipt_number))
        {
            $errors->add('Shipment', 'Tidak dapat menghapus data barang yang telah dikirim.');
        }

        if($errors->count())
        {
            $model['errors']                = $errors;

            return false;
        }

        return true;
    }
}
