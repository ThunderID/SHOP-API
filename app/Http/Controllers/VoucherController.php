<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
{
    /**
     * Display all products
     *
     * @return Response
     */
    public function index()
    {
        $result                 = new \App\Models\Voucher;

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

        $result                     = $result->with(['quotalogs'])->get()->toArray();

        return new JSend('success', (array)$result);
    }

    /**
     * Display a product
     *
     * @return Response
     */
    public function detail($id = null)
    {
        $result                 = \App\Models\Voucher::id($id)->with(['quotalogs', 'sales', 'sales.customer'])->first();

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
    public function store()
    {
        if(!Input::has('voucher'))
        {
            return new JSend('error', (array)Input::all(), 'Tidak ada data voucher.');
        }

        $errors                     = new MessageBag();

        DB::beginTransaction();

        //1. Validate Voucher Parameter
        if(is_null($voucher['id']))
        {
            $is_new                 = true;
        }
        else
        {
            $is_new                 = false;
        }

        $voucher_rules             =   [
                                            'user_id'                       => 'required|exists:users,id',
                                            'code'                          => 'required|max:255|unique:vouchers,upc,'.(!is_null($voucher['id']) ? $voucher['id'] : ''),
                                            'type'                          => 'required|in:debit_point,free_shipping_cost',
                                            'value'                         => 'required|numeric',
                                            'started_at'                    => 'date_format:"Y-m-d H:i:s"',
                                            'expired_at'                    => 'date_format:"Y-m-d H:i:s"',
                                        ];

        //1a. Get original data
        $voucher_data              = \App\Models\Voucher::findornew($voucher['id']);

        //1b. Validate Basic Voucher Parameter
        $validator                  = Validator::make($voucher, $voucher_rules);

        if (!$validator->passes())
        {
            $errors->add('Voucher', $validator->errors());
        }
        else
        {
            //if validator passed, save voucher
            $voucher_data           = $voucher_data->fill($voucher);

            if(!$voucher_data->save())
            {
                $errors->add('Voucher', $voucher_data->getError());
            }
        }

        //2. Validate Voucher Detail Parameter
        if(!$errors->count())
        {
            $log_current_ids         = [];
            foreach ($voucher['quotalogs'] as $key => $value) 
            {
                if(!$errors->count())
                {
                    $log_data        = \App\Models\QuotaLog::find($value['id']);

                    if($log_data)
                    {
                        $log_rules   =   [
                                                'voucher_id'                    => 'required|numeric|'.($is_new ? '' : 'in:'.$voucher_data['id']),
                                                'amount'                        => 'required|numeric|in:'.$log_data['amount'],
                                                'notes'                         => 'required|max:512|in:'.$log_data['notes'],
                                            ];

                        $validator      = Validator::make($log_data['attributes'], $log_rules);
                    }
                    else
                    {
                        $log_rules   =   [
                                                'voucher_id'                    => 'numeric|'.($is_new ? '' : 'in:'.$voucher_data['id']),
                                                'amount'                        => 'required|numeric',
                                                'notes'                         => 'required|max:512',
                                            ];

                        $validator      = Validator::make($value, $log_rules);
                    }

                    //if there was log and validator false
                    if ($log_data && !$validator->passes())
                    {
                        if($value['voucher_id']!=$voucher['id'])
                        {
                            $errors->add('Log', 'Voucher dari Log Tidak Valid.');
                        }
                        elseif($is_new)
                        {
                            $errors->add('Log', 'Voucher Log Tidak Valid.');
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
                        $value['voucher_id']        = $voucher_data['id'];

                        $log_data                    = new \App\Models\QuotaLog;

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
                    $logs                            = \App\Models\QuotaLog::voucherid($voucher['id'])->get()->toArray();
                    
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
                            $log_data                = \App\Models\QuotaLog::find($value);

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
        
        $final_voucher                 = \App\Models\Voucher::id($voucher_data['id'])->with(['quotalogs', 'transactions'])->first()->toArray();

        return new JSend('success', (array)$final_voucher);
    }

    /**
     * Delete a voucher
     *
     * @return Response
     */
    public function delete($id = null)
    {
        //
        $voucher                    = \App\Models\Voucher::id($id)->with(['quotalogs', 'transactions'])->first()->toArray();

        $result                     = $voucher;

        if($voucher->delete())
        {
            return new JSend('success', (array)$result);
        }

        return new JSend('error', (array)$result, $voucher->getError());
    }
}
