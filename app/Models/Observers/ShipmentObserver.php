<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;

use App\Models\ShippingCost;
use App\Models\Sale;

/**
 * Used in Shipment model
 *
 * @author cmooy
 */
class ShipmentObserver 
{
    /** 
     * observe payment event saving
     * 1. recalculate shipping_cost
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
    public function saving($model)
    {
        $errors                             = new MessageBag();

        //1. recalculate shipping_cost
        $shippingcost                       = ShippingCost::courierid($model->courier_id)->postalcode($model->address->zipcode)->first();

        if($shippingcost && $model->transaction()->count() && $model->transaction->transactiondetails()->count())
        {
            $shipping_cost                  = $model->CountShippingCost($model->transaction->transactiondetails, $shippingcost['cost']);
           
            $transaction                    = Sale::findorfail($model->transaction_id);

            $transaction->fill(['shipping_cost' => $shipping_cost]);
            
            if(!$transaction->save())
            {
                $errors->add('Shipment', $transaction->getError());
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

    /** 
     * observe payment event updated
     * 1. recalculate shipping_cost
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
    public function updated($model)
    {
        $errors                             = new MessageBag();

        //1. check receipt_number
        if(!is_null($model->receipt_number) && $model->transaction()->count())
        {
            $result                             = $model->ChangeStatus($model->transaction, 'shipping');

            if(!$result)
            {
                return false;
            }
        }

        if($errors->count())
        {
            $model['errors']                = $errors;

            return false;
        }

        return true;
    }

    /** 
     * observe payment event deleting
     * 1. recalculate shipping_cost
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
    public function deleting($model)
    {
        $errors                             = new MessageBag();

        //1. check receipt_number
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
