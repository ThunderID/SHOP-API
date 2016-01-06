<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MyOrderController extends Controller
{
    /**
     * Display all customer's recorded orders
     *
     * @return Response
     */
    public function index($user_id = null)
    {
        $result                 = \App\Models\Sale::userid($user_id)->status(['wait', 'canceled', 'paid', 'shipping', 'packed', 'delivered'])->get()->toArray();

        return new JSend('success', (array)$result);
    }

    /**
     * Display an order by customer
     *
     * @return Response
     */
    public function detail($user_id = null, $order_id = null)
    {
        $result                 = \App\Models\Sale::userid($user_id)->id($order_id)->status(['wait', 'canceled', 'paid', 'shipping', 'packed', 'delivered'])->with(['orderlogs', 'transactiondetails', 'transactiondetails.varian', 'transactiondetails.varian.product', 'shipment', 'shipment.address'])->first();

        if($result)
        {
            return new JSend('success', (array)$result->toArray());
        }

        return new JSend('error', (array)Input::all(), 'ID Tidak Valid.');
    }

    /**
     * Display an order from customer's cart
     *
     * @return Response
     */
    public function incart($user_id = null)
    {
        $result                 = \App\Models\Sale::userid($user_id)->status('cart')->with(['transactiondetails', 'transactiondetails.varian', 'transactiondetails.varian.product'])->first();

        if($result)
        {
            return new JSend('success', (array)$result->toArray());
        }
        
        return new JSend('error', (array)Input::all(), 'ID Tidak Valid.');
    }


    /**
     * Display store customer order
     *
     * @return Response
     */
    public function store()
    {
        if(!Input::has('order'))
        {
            return new JSend('error', (array)Input::all(), 'Tidak ada data order.');
        }

        $errors                     = new MessageBag();

        $order                      = Input::get('order');

        DB::beginTransaction();

        $order                      = Input::get('order');

        if(is_null($order['id']))
        {
            $is_new                 = true;
        }
        else
        {
            $is_new                 = false;
        }

        $order_rules             =  [
        'user_id'                   => 'required|exists:users,id',
        'voucher_id'                => 'numeric',
        ];

        //1a. Get original data
        $order_data              = \App\Models\Sale::findornew($order['id']);

        //1b. Validate Basic Order Parameter
        $validator                  = Validator::make($order, $order_rules);

        if (!$validator->passes())
        {
            $errors->add('Sale', $validator->errors());
        }
        else
        {
            //if validator passed, save order
            $order_data           = $order_data->fill(['user_id' => $order['user_id'], 'voucher_id' => $order['voucher_id']]);

            if(!$order_data->save())
            {
                $errors->add('Sale', $order_data->getError());
            }
        }

        //2. Validate Order Detail Parameter
        if(!$errors->count())
        {
            $detail_current_ids         = [];
            foreach ($order['transactiondetails'] as $key => $value) 
            {
                if(!$errors->count())
                {
                    $detail_data        = \App\Models\TransactionDetail::find($value['id']);

                    if($detail_data)
                    {
                        $detail_rules   =   [
                        'transaction_id'            => 'required|numeric|'.($is_new ? '' : 'in:'.$order_data['id']),
                        'varian_id'                 => 'required|max:255|in:'.$detail_data['varian_id'],
                        'quantity'                  => 'required|max:255|in:'.$detail_data['quantity'],
                        'price'                     => 'required|max:255|in:'.$detail_data['price'],
                        'discount'                  => 'required|max:255|in:'.$detail_data['discount'],
                        ];

                        $validator      = Validator::make($detail_data['attributes'], $detail_rules);
                    }
                    else
                    {
                        $detail_rules   =   [
                        'transaction_id'            => 'numeric|'.($is_new ? '' : 'in:'.$order_data['id']),
                        'varian_id'                 => 'required|max:255|',
                        'quantity'                  => 'required|numeric|',
                        'price'                     => 'required|numeric|',
                        'discount'                  => 'required|numeric|',
                        ];

                        $validator      = Validator::make($value, $detail_rules);
                    }

                    //if there was detail and validator false
                    if ($detail_data && !$validator->passes())
                    {
                        if($value['transaction_id']!=$order['id'])
                        {
                            $errors->add('Detail', 'Produk dari Detail Tidak Valid.');
                        }
                        elseif($is_new)
                        {
                            $errors->add('Detail', 'Produk Detail Tidak Valid.');
                        }
                        else
                        {
                            $detail_data                = $detail_data->fill($value);

                            if(!$detail_data->save())
                            {
                                $errors->add('Detail', $detail_data->getError());
                            }
                            else
                            {
                                $detail_current_ids[]   = $detail_data['id'];
                            }
                        }
                    }
                    //if there was detail and validator false
                    elseif (!$detail_data && !$validator->passes())
                    {
                        $errors->add('Detail', $validator->errors());
                    }
                    elseif($detail_data && $validator->passes())
                    {
                        $detail_current_ids[]           = $detail_data['id'];
                    }
                    else
                    {
                        $value['transaction_id']        = $order_data['id'];

                        $detail_data                    = new \App\Models\TransactionDetail;

                        $detail_data                    = $detail_data->fill($value);

                        if(!$detail_data->save())
                        {
                            $errors->add('Detail', $detail_data->getError());
                        }
                        else
                        {
                            $detail_current_ids[]       = $detail_data['id'];
                        }
                    }
                }
            }

            //if there was no error, check if there were things need to be delete
            if(!$errors->count())
            {
                $details                            = \App\Models\TransactionDetail::transactionid($order['id'])->get()->toArray();
                
                $detail_should_be_ids               = [];
                foreach ($details as $key => $value) 
                {
                    $detail_should_be_ids[]         = $value['id'];
                }

                $difference_detail_ids              = array_diff($detail_should_be_ids, $detail_current_ids);

                if($difference_detail_ids)
                {
                    foreach ($difference_detail_ids as $key => $value) 
                    {
                        $detail_data                = \App\Models\TransactionDetail::find($value);

                        if(!$detail_data->delete())
                        {
                            $errors->add('Detail', $detail_data->getError());
                        }
                    }
                }
            }
        }

        //2. Check if need to save address
        if(!$errors->count() && isset($order['shipment']['address']))
        {
            $address_data        = \App\Models\Address::findornew($order['shipment']['address']['id']);

            $address_rules      =   [
            'phone'                         => 'required|numeric',
            'address'                       => 'required',
            'zipcode'                       => 'required|numeric',
            ];

            $validator          = Validator::make($order['shipment']['address'], $address_rules);

            //2a. save address
            //if there was address and validator false
            if (!$validator->passes())
            {
                $errors->add('Sale', $validator->errors());
            }
            else
            {
                //if validator passed, save address
                $order['shipment']['address']['owner_id']          = $order['user_id'];
                $order['shipment']['address']['owner_type']        = 'App\Models\Customer';

                $address_data       = $address_data->fill($order['shipment']['address']);

                if(!$address_data->save())
                {
                    $errors->add('Sale', $address_data->getError());
                }
            }

            //2b. save shipment
            if(!$errors->count())
            {
                $shipment_data      = \App\Models\Shipment::findornew($order['shipment']['id']);

                $shipment_rules     =   [
                'courier_id'                        => 'required|numeric',
                'receiver_name'                     => 'required|max:255',
                ];

                $validator          = Validator::make($order['shipment'], $shipment_rules);

                //2a. save shipment
                //if there was shipment and validator false
                if (!$validator->passes())
                {
                    $errors->add('Sale', $validator->errors());
                }
                else
                {
                    //if validator passed, save shipment
                    $order['shipment']['transaction_id']    = $order['id'];
                    $order['shipment']['address_id']        = $address_data['id'];

                    $shipment_data       = $shipment_data->fill($order['shipment']);

                    if(!$shipment_data->save())
                    {
                        $errors->add('Sale', $shipment_data->getError());
                    }
                }
            }
        }

        //3. yupdate status
        if(!$errors->count() && $order_data['status'] != $order['status'])
        {
            $log_data                    = new \App\Models\TransactionLog;

            $log_data                    = $log_data->fill(['status' => $order_data['status'], 'transaction_id' => $order_data['id']]);

            if(!$log_data->save())
            {
                $errors->add('Log', $log_data->getError());
            }
        }

        if($errors->count())
        {
            DB::rollback();

            return new JSend('error', (array)Input::all(), $errors);
        }

		DB::commit();
        
        $final_order                 = \App\Models\Sale::userid($user_id)->id($order_id)->status(['cart', 'wait', 'canceled', 'paid', 'shipping', 'packed', 'delivered'])->with(['orderlogs', 'transactiondetails', 'transactiondetails.varian', 'transactiondetails.varian.product'])->first();

        return new JSend('success', (array)$final_order);
    }
}
