<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use Request;

class MyOrderController extends Controller
{
    /**
     * Display all customer's recorded orders
     *
     * @return Response
     */
    public function index($user_id = null)
    {
        $result                     = \App\Models\Sale::userid($user_id)->status(['wait', 'canceled', 'paid', 'shipping', 'packed', 'delivered']);

        $count                      = count($result->get());

        if(Input::has('skip'))
        {
            $skip                   = Input::get('skip');
            $result                 = $result->skip($skip);
        }

        if(Input::has('take'))
        {
            $take                   = Input::get('take');
            $result                 = $result->take($take);
        }

        $result                     = $result->get()->toArray();

        return new JSend('success', (array)['count' => $count, 'data' => $result]);
    }

    /**
     * Display an order by customer
     *
     * @return Response
     */
    public function detail($user_id = null, $order_id = null)
    {
        $result                 = \App\Models\Sale::userid($user_id)->id($order_id)->status(['wait', 'canceled', 'paid', 'shipping', 'packed', 'delivered'])->with(['orderlogs', 'transactiondetails', 'transactiondetails.varian', 'transactiondetails.varian.product', 'shipment', 'shipment.courier', 'shipment.address'])->first();

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
        
        return new JSend('error', (array)Input::all(), 'Tidak ada cart.');
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

        $user_id                    = Request::route()[2]['user_id'];

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
            // 'user_id'                   => 'required|exists:users,id',
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
            $order_data           = $order_data->fill(['user_id' => $user_id, 'voucher_id' => (isset($order['voucher_id']) ? $order['voucher_id'] : '0')]);

            if(!$order_data->save())
            {
                $errors->add('Sale', $order_data->getError());
            }
        }

        //2. Validate Order Detail Parameter
        if(!$errors->count() && isset($order['transactiondetails']) && is_array($order['transactiondetails']))
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
                        'varian_id'                 => 'required|max:255|exists:varians,id',
                        'quantity'                  => 'required|numeric',
                        'price'                     => 'required|numeric',
                        'discount'                  => 'numeric',
                        ];

                        $validator      = Validator::make($detail_data['attributes'], $detail_rules);
                    }
                    else
                    {
                        $detail_rules   =   [
                        'transaction_id'            => 'numeric|'.($is_new ? '' : 'in:'.$order_data['id']),
                        'varian_id'                 => 'required|max:255|exists:varians,id',
                        'quantity'                  => 'required|numeric',
                        'price'                     => 'required|numeric',
                        'discount'                  => 'numeric',
                        ];

                        $validator      = Validator::make($value, $detail_rules);
                    }

                    //if there was detail and validator false
                    if ($detail_data && !$validator->passes())
                    {
                        if($value['transaction_id']!=$order['id'])
                        {
                            $errors->add('Detail', 'Produk di keranjang Tidak Valid.');
                        }
                        elseif($is_new)
                        {
                            $errors->add('Detail', 'Produk di keranjang Tidak Valid.');
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
            'phone'                         => 'required|max:255',
            'address'                       => 'required',
            'zipcode'                       => 'required|max:255',
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
                $order['shipment']['address']['owner_id']          = $user_id;
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
                if($order_data->shipment->count())
                {
                    $shipment_data      = \App\Models\Shipment::findorfail($order_data->shipment->id);
                }
                else
                {
                    $shipment_data      = \App\Models\Shipment::findornew($order['shipment']['id']);
                }

                $shipment_rules     =   [
                    'courier_id'                        => 'required|numeric|exists:couriers,id',
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

        //3. update status
        if(!$errors->count() && isset($order['status']) && $order_data['status'] != $order['status'])
        {
            $log_data                    = new \App\Models\TransactionLog;

            $log_data                    = $log_data->fill(['status' => $order['status'], 'transaction_id' => $order_data['id']]);

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
        
        $final_order                 = \App\Models\Sale::userid($user_id)->id($order_data['id'])->status(['cart', 'wait', 'canceled', 'paid', 'shipping', 'packed', 'delivered'])->with(['orderlogs', 'transactiondetails', 'transactiondetails.varian', 'transactiondetails.varian.product', 'shipment', 'shipment.courier', 'shipment.address'])->first()->toArray();

        return new JSend('success', (array)$final_order);
    }
}
