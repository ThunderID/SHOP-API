<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Authenticate user
     *
     * @return Response
     */
    public function signin()
    {
        $email                          = Input::get('email');
        $password                       = Input::get('password');
        
        $check                          = Auth::attempt(['email' => $email, 'password' => $password]);

        if ($check)
        {
            $result                     = Auth::user();

            return new JSend('success', (array)$result);
        }
        
        return new JSend('error', (array)Input::all(), 'Username atau password tidak valid.');
    }

    /**
     * Register customer
     *
     * @return Response
     */
    public function signup()
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
     * Register customer
     *
     * @return Response
     */
    public function activate()
    {
        if(!Input::has('link'))
        {
            return new JSend('error', (array)Input::all(), 'Tidak ada data customer.');
        }

        $link                       = Input::get('link');

        $errors                     = new MessageBag();

        DB::beginTransaction();

        //1. Check Link
        $customer_data              = \App\Models\Customer::activationlink($link);

        if(!$customer_data)
        {
            $errors->add('Customer', 'Link tidak valid.');
        }
        elseif(!$customer_data->is_active)
        {
            $errors->add('Customer', 'Link tidak valid.');
        }
        else
        {
            //if validator passed, save customer
            $customer_data           = $customer_data->fill(['is_active' => true]);

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
}
