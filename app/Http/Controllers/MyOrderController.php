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
    public function index($id = null)
    {
        $result                 = \App\Models\Sale::userid($id)->status(['wait', 'canceled', 'paid', 'shipping', 'packed', 'delivered'])->get()->toArray();

        return new JSend('success', (array)$result);
    }

    /**
     * Display an order by customer
     *
     * @return Response
     */
    public function detail($id = null, $order_id = null)
    {
        $result                 = \App\Models\Sale::userid($id)->id($order_id)->status(['wait', 'canceled', 'paid', 'shipping', 'packed', 'delivered'])->first()->toArray();

        return new JSend('success', (array)$result);
    }

    /**
     * Display an order from customer's cart
     *
     * @return Response
     */
    public function incart($id = null, $order_id = null)
    {
        $result                 = \App\Models\Sale::userid($id)->id($order_id)->status('cart')->first();

        return new JSend('success', (array)$result);
    }


    /**
     * Display store customer order
     *
     * @return Response
     */
    public function store()
    {
        if(!Input::has('purchase'))
        {
            return new JSend('error', (array)Input::all(), 'Tidak ada data purchase.');
        }

        $errors                     = new MessageBag();

        DB::beginTransaction();

        //1. Validate Purchase Parameter
        if(is_null($purchase['id']))
        {
            $is_new                 = true;
        }
        else
        {
            $is_new                 = false;
        }

        $purchase_rules             =   [
                                            'supplier_id'                   => 'required|exists:suppliers,id'
                                        ];

        //1a. Get original data
        $purchase_data              = \App\Models\Purchase::findornew($purchase['id']);

        //1b. Validate Basic Purchase Parameter
        $validator                  = Validator::make($purchase, $purchase_rules);

        if (!$validator->passes())
        {
            $errors->add('Purchase', $validator->errors());
        }
        else
        {
            //if validator passed, save purchase
            $purchase_data           = $purchase_data->fill(['supplier_id' => $purchase['supplier_id']]);

            if(!$purchase_data->save())
            {
                $errors->add('Purchase', $purchase_data->getError());
            }
        }

        //2. Validate Purchase Detail Parameter
        if(!$errors->count())
        {
            $detail_current_ids         = [];
            foreach ($purchase['transactiondetails'] as $key => $value) 
            {
                if(!$errors->count())
                {
                    $detail_data        = \App\Models\TransactionDetail::find($value['id']);

                    if($detail_data)
                    {
                        $detail_rules   =   [
                                                'transaction_id'            => 'required|numeric|'.($is_new ? '' : 'in:'.$purchase_data['id']),
                                                'sku'                       => 'required|max:255|in:'.$detail_data['sku'].'unique:transaction_details,sku,'.(!is_null($detail_data['id']) ? $detail_data['id'] : ''),
                                                'size'                      => 'required|max:255|in:'.$detail_data['size'],
                                            ];

                        $validator      = Validator::make($detail_data['attributes'], $detail_rules);
                    }
                    else
                    {
                        $detail_rules   =   [
                                                'transaction_id'            => 'numeric|'.($is_new ? '' : 'in:'.$purchase_data['id']),
                                                'sku'                       => 'required|max:255|unique:transaction_details,sku,'.(!is_null($value['id']) ? $value['id'] : ''),
                                                'size'                      => 'required|max:255|',
                                            ];

                        $validator      = Validator::make($value, $detail_rules);
                    }

                    //if there was detail and validator false
                    if ($detail_data && !$validator->passes())
                    {
                        if($value['transaction_id']!=$purchase['id'])
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
                        $value['transaction_id']        = $purchase_data['id'];

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

                //if there was no error, check if there were things need to be delete
                if(!$errors->count())
                {
                    $details                            = \App\Models\TransactionDetail::transactionid($purchase['id'])->get()->toArray();
                    
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
        }

        //3. Validate Purchase Status Parameter
        if(!$errors->count())
        {
            $log_current_ids         = [];
            foreach ($purchase['transactionlogs'] as $key => $value) 
            {
                if(!$errors->count())
                {
                    $log_data        = \App\Models\TransactionLog::find($value['id']);

                    if($log_data)
                    {
                        $log_rules   =   [
                                                'transaction_id'            => 'required|numeric|'.($is_new ? '' : 'in:'.$purchase_data['id']),
                                                'sku'                       => 'required|max:255|in:'.$log_data['sku'].'unique:transaction_logs,sku,'.(!is_null($log_data['id']) ? $log_data['id'] : ''),
                                                'size'                      => 'required|max:255|in:'.$log_data['size'],
                                            ];

                        $validator      = Validator::make($log_data['attributes'], $log_rules);
                    }
                    else
                    {
                        $log_rules   =   [
                                                'transaction_id'            => 'numeric|'.($is_new ? '' : 'in:'.$purchase_data['id']),
                                                'sku'                       => 'required|max:255|unique:transaction_logs,sku,'.(!is_null($value['id']) ? $value['id'] : ''),
                                                'size'                      => 'required|max:255|',
                                            ];

                        $validator      = Validator::make($value, $log_rules);
                    }

                    //if there was log and validator false
                    if ($log_data && !$validator->passes())
                    {
                        if($value['transaction_id']!=$purchase['id'])
                        {
                            $errors->add('Log', 'Produk dari Log Tidak Valid.');
                        }
                        elseif($is_new)
                        {
                            $errors->add('Log', 'Produk Log Tidak Valid.');
                        }
                        else
                        {
                            $log_data                = $log_data->fill($value);

                            if(!$log_data->save())
                            {
                                $errors->add('Log', $log_data->getError());
                            }
                            else
                            {
                                $log_current_ids[]   = $log_data['id'];
                            }
                        }
                    }
                    //if there was log and validator false
                    elseif (!$log_data && !$validator->passes())
                    {
                        $errors->add('Log', $validator->errors());
                    }
                    elseif($log_data && $validator->passes())
                    {
                        $log_current_ids[]           = $log_data['id'];
                    }
                    else
                    {
                        $value['transaction_id']        = $purchase_data['id'];

                        $log_data                    = new \App\Models\TransactionLog;

                        $log_data                    = $log_data->fill($value);

                        if(!$log_data->save())
                        {
                            $errors->add('Log', $log_data->getError());
                        }
                        else
                        {
                            $log_current_ids[]       = $log_data['id'];
                        }
                    }
                }

                //if there was no error, check if there were things need to be delete
                if(!$errors->count())
                {
                    $logs                            = \App\Models\TransactionLog::transactionid($purchase['id'])->get()->toArray();
                    
                    $log_should_be_ids               = [];
                    foreach ($logs as $key => $value) 
                    {
                        $log_should_be_ids[]         = $value['id'];
                    }

                    $difference_log_ids              = array_diff($log_should_be_ids, $log_current_ids);

                    if($difference_log_ids)
                    {
                        foreach ($difference_log_ids as $key => $value) 
                        {
                            $log_data                = \App\Models\TransactionLog::find($value);

                            if(!$log_data->delete())
                            {
                                $errors->add('Log', $log_data->getError());
                            }
                        }
                    }
                }
            }
        }

        if($errors->count())
        {
            DB::rollback();

            return new JSend('error', (array)Input::all(), $errors);
        }

        DB::commit();
        
        $final_purchase                 = \App\Models\Purchase::id($purchase_data['id'])->with(['transactionlogs', 'supplier', 'transactiondetails', 'transactiondetails.varian', 'transactiondetails.varian.product'])->first();

        return new JSend('success', (array)$final_purchase);
    }
}
