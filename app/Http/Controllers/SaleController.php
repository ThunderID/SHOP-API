<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    /**
     * Display all products
     *
     * @return Response
     */
    public function index()
    {
        $result                 = new \App\Models\Sale;

        if(Input::has('search'))
        {
            $search                 = Input::get('search');

            foreach ($search as $key => $value) 
            {
                switch (strtolower($key)) 
                {
                    default:
                        # code...
                        break;
                }
            }
        }

        $result                     = $result->with(['user'])->get()->toArray();

        return new JSend('success', (array)$result);
    }

    /**
     * Display a product
     *
     * @return Response
     */
    public function detail($id = null)
    {
        $result                 = \App\Models\Sale::id($id)->with(['transactionlogs', 'user', 'transactiondetails', 'transactiondetails.varian', 'transactiondetails.varian.product', 'paidpointlogs', 'payment', 'shipment', 'shipment.address'])->first();

        if($result)
        {
            return new JSend('success', (array)$result->toArray());

        }
        return new JSend('error', (array)Input::all(), 'ID Tidak Valid.');
    }

    /**
     * Store a product
     *
     * @return Response
     */
    public function status()
    {
        if(!Input::has('sale'))
        {
            return new JSend('error', (array)Input::all(), 'Tidak ada data sale.');
        }

        $errors                     = new MessageBag();

        DB::beginTransaction();

        //1. Validate Sale Parameter
        $sale                       = Input::get('sale');

        if(is_null($sale['id']))
        {
            return new JSend('error', (array)Input::all(), 'Tidak ada data sale.');
        }
        else
        {
            $is_new                 = false;
        }


        //1a. Get original data
        $sale_data              = \App\Models\Sale::findorfail($sale['id']);

        //1b. Check if there were statuses differences
        if($sale_data['status']==$sale['status'])
        {
            $errors->add('Sale', 'Tidak ada perubahan status.');
        }

        //2. Check if status = paid
        if(!$errors->count() && in_array($sale['status'], ['paid']))
        {
            if(is_null($sale['payment']))
            {
                $errors->add('Sale', 'Tidak ada data pembayaran.');
            }
            else
            {
                $payment_rule   =   [
                                    'transaction_id'            => 'required|numeric|in:'.$sale_data['id'],
                                    'method'                    => 'required|max:255',
                                    'destination'               => 'required|max:255',
                                    'account_name'              => 'required|max:255',
                                    'account_number'            => 'required|max:255',
                                    'ondate'                    => 'required|date_format:"Y-m-d H:i:s"',
                                    'amount'                    => 'required|numeric|in:'.$sale_data['bills'],
                                ];

                $validator   = Validator::make($sale['payment'], $payment_rule);

                //if there was log and validator false
                if (!$validator->passes())
                {
                    $errors->add('Log', 'Payment tidak valid.');
                }
                else
                {
                    $paid_data                    = new \App\Models\Payment;

                    $paid_data                    = $paid_data->fill($sale['payment']);

                    if(!$paid_data->save())
                    {
                        $errors->add('Log', $paid_data->getError());
                    }
                }
            }
        }

        //2. Check if status = shipping
        if(!$errors->count() && in_array($sale['status'], ['shipping']))
        {
            if(is_null($sale['shipment']['receipt_number']))
            {
                $errors->add('Sale', 'Tidak ada nomor resi.');
            }
            else
            {
                $shipping_data   = \App\Models\Shipment::id($sale['shipment']['id'])->first();

                if($shipping_data)
                {
                    $shipment_rule   =  [
                                            'receipt_number'            => 'required|max:255',
                                        ];

                    $validator   = Validator::make($sale['shipment'], $shipment_rule);

                    //if there was log and validator false
                    if (!$validator->passes())
                    {
                        $errors->add('Log', 'Shipment tidak valid.');
                    }
                    else
                    {
                        $shipping_data                    = $shipping_data->fill($sale['shipment']);

                        if(!$shipping_data->save())
                        {
                            $errors->add('Log', $shipping_data->getError());
                        }
                    }
                }
                else
                {
                    $errors->add('Log', 'Shipment tidak valid.');
                }
            }
        }

        //4. Check if status = others
        if(!$errors->count() && !in_array($sale['status'], ['paid', 'shipping']))
        {
            $log_rules   =   [
                                    'transaction_id'            => 'numeric|'.($is_new ? '' : 'in:'.$sale_data['id']),
                                    'status'                    => 'required|max:255|in:cart,wait,paid,packed,shipping,delivered,canceled,abandoned',
                                ];

            $validator   = Validator::make($value, $log_rules);

            //if there was log and validator false
            if ($log_data && !$validator->passes())
            {
                $errors->add('Log', 'Status dari Log Tidak Valid.');
            }
            else
            {
                $log_data                    = new \App\Models\TransactionLog;

                $log_data                    = $log_data->fill(['status' => $sale['status'], 'transaction_id' => $sale_data['id']]);

                if(!$log_data->save())
                {
                    $errors->add('Log', $log_data->getError());
                }
            }
        }


        if($errors->count())
        {
            DB::rollback();

            return new JSend('error', (array)Input::all(), $errors);
        }

        DB::commit();
        
        $final_sale                 = \App\Models\Sale::id($sale_data['id'])->with(['transactionlogs', 'user', 'transactiondetails', 'transactiondetails.varian', 'transactiondetails.varian.product'])->first();

        return new JSend('success', (array)$final_sale);
    }
}
