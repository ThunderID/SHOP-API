<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Display all products
     *
     * @return Response
     */
    public function index()
    {
        $result                 = new \App\Models\Admin;

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

        $result                     = $result->with(['audits'])->get()->toArray();

        return new JSend('success', (array)$result);
    }

    /**
     * Display a product
     *
     * @return Response
     */
    public function detail($id = null)
    {
        $result                 = \App\Models\Admin::id($id)->with(['audits'])->first();

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
        if(!Input::has('admin'))
        {
            return new JSend('error', (array)Input::all(), 'Tidak ada data admin.');
        }

        $errors                     = new MessageBag();

        DB::beginTransaction();

        //1. Validate Admin Parameter
        $admin                    = Input::get('admin');
        
        if(is_null($admin['id']))
        {
            $is_new                 = true;
        }
        else
        {
            $is_new                 = false;
        }

        $admin_rules                =   [
                                            'name'                          => 'required|max:255',
                                            'email'                         => 'required|max:255|unique:users,email,'.(!is_null($admin['id']) ? $admin['id'] : ''),
                                            // 'role'                          => 'required|max:255',
                                            'is_active'                     => 'boolean',
                                            'gender'                        => 'required|in:male,female',
                                            'date_of_birth'                 => 'required|date_format:"Y-m-d H:i:s"',
                                        ];

        //1a. Get original data
        $admin_data                 = \App\Models\Admin::findornew($admin['id']);

        //1b. Validate Basic Admin Parameter
        $validator                  = Validator::make($admin, $admin_rules);

        if (!$validator->passes())
        {
            $errors->add('Admin', $validator->errors());
        }
        else
        {
            //if validator passed, save admin
            $admin_data           = $admin_data->fill($admin);

            if(!$admin_data->save())
            {
                $errors->add('Admin', $admin_data->getError());
            }
        }

        if($errors->count())
        {
            DB::rollback();

            return new JSend('error', (array)Input::all(), $errors);
        }

        DB::commit();
        
        $final_admin                 = \App\Models\Admin::id($admin_data['id'])->with(['audits'])->first()->toArray();

        return new JSend('success', (array)$final_admin);
    }
}