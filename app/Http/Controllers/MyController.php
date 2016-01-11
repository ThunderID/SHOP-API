<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MyController extends Controller
{
    /**
     * Display a customer by me
     *
     * @return Response
     */
    public function detail($user_id = null)
    {
        $result                 = \App\Models\Customer::id($user_id)->with(['myreferrals', 'myreferrals.user'])->first();

        if($result)
        {
            return new JSend('success', (array)$result->toArray());
        }
        
        return new JSend('error', (array)Input::all(), 'ID Tidak Valid.');
    }

    /**
     * Display my points
     *
     * @return Response
     */
    public function points($user_id = null)
    {
        $result                 = \App\Models\PointLog::userid($user_id)->get()->toArray();

        return new JSend('success', (array)$result);
    }

    /**
     * Register customer
     *
     * @return Response
     */
    public function store()
    {
        if(!Input::has('customer'))
        {
            return new JSend('error', (array)Input::all(), 'Tidak ada data customer.');
        }

        $customer                   = Input::get('customer');

        $errors                     = new MessageBag();

        DB::beginTransaction();

        //1. Validate User Parameter
        if(is_null($customer['id']))
        {
            $is_new                 = true;
        }
        else
        {
            $is_new                 = false;
        }

        $customer_rules             =   [
                                            'name'                          => 'required|max:255',
                                            'email'                         => 'required|max:255|unique:customers,email,'.(!is_null($customer['id']) ? $customer['id'] : ''),
                                            'password'                      => 'max:255',
                                            'sso_id'                        => '',
                                            'sso_media'                     => 'in:facebook',
                                            'sso_data'                      => '',
                                            'gender'                        => 'in:male,female',
                                            'date_of_birth'                 => 'date_format:"Y-m-d H:i:s"',
                                        ];

        //1a. Get original data
        $customer_data              = \App\Models\Customer::findornew($customer['id']);

        //1b. Validate Basic Customer Parameter
        $validator                  = Validator::make($customer, $customer_rules);

        if (!$validator->passes())
        {
            $errors->add('Customer', $validator->errors());
        }
        else
        {
            //if validator passed, save customer
            $customer_data           = $customer_data->fill($customer);

            if(!$customer_data->save())
            {
                $errors->add('Customer', $customer_data->getError());
            }
        }

        if($errors->count())
        {
            DB::rollback();

            return new JSend('error', (array)Input::all(), $errors);
        }

        DB::commit();
        
        $final_customer                 = \App\Models\Customer::id($customer_data['id'])->first()->toArray();

        return new JSend('success', (array)$final_customer);
    }

    /**
     * Redeem code
     *
     * @return Response
     */
    public function redeem($user_id = null)
    {
        if(!Input::has('code'))
        {
            return new JSend('error', (array)Input::all(), 'Tidak ada data code.');
        }

        $code                       = Input::get('code');

        $errors                     = new MessageBag();

        DB::beginTransaction();

        //1. Check Link
        $voucher_data              = \App\Models\Voucher::code($code)->ondate('now')->type(['referral', 'promo_referral'])->first();

        if(!$voucher_data)
        {
            $errors->add('Redeem', 'Code tidak valid.');
        }
        elseif(!$voucher_data->quota > 0)
        {
            $errors->add('Redeem', 'Quota sudah habis.');
        }
        else
        {
            //if validator passed, save voucher
            $point                  =   [
                                            'user_id'               => $user_id,
                                            'reference_id'          => $voucher_data['user_id'],
                                            'reference_type'        => '\App\Models\User',
                                        ];

            $point_data             = new \App\Models\PointLog;
            
            $point_data->fill($point);

            if(!$point_data->save())
            {
                $errors->add('Redeem', $point_data->getError());
            }
        }

        if($errors->count())
        {
            DB::rollback();

            return new JSend('error', (array)Input::all(), $errors);
        }

        DB::commit();
        
        $final_costumer                 = \App\Models\Customer::id($user_id)->with(['myreferrals', 'myreferrals.user'])->first()->toArray();

        return new JSend('success', (array)$final_costumer);
    }
}
