<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PointController extends Controller
{
    /**
     * Display all points
     *
     * @return Response
     */
    public function index()
    {
        $result                 = new \App\Models\PointLog;

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
        
        $count                      = $result->count();

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

        $result                     = $result->with(['user'])->get()->toArray();
dd($result);
        return new JSend('success', (array)['count' => $count, 'data' => $result]);
    }

    /**
     * Store a point
     *
     * @return Response
     */
    public function store()
    {
        if(!Input::has('point'))
        {
            return new JSend('error', (array)Input::all(), 'Tidak ada data point.');
        }

        $errors                     = new MessageBag();

        DB::beginTransaction();

        //1. Validate Point Parameter
        $point                       = Input::get('point');

        if(is_null($point['id']))
        {
            $is_new                 = true;
        }
        else
        {
            $is_new                 = false;
        }


        //1. Get original data
        $point_data                 = \App\Models\PointLog::findornew($point['id']);

        if(!$errors->count())
        {
            $point_rules   =   [
                                                'user_id'                   => 'required|numeric|'.($is_new ? '' : 'in:'.$point_data['user_id']),
                                                'amount'                    => 'required|numeric',
                                                'expired_at'                => 'required|date_format:"Y-m-d H:i:s"',
                                                'notes'                     => 'required',
                                            ];

            $validator      = Validator::make($point, $point_rules);

            if (!$validator->passes())
            {
                $errors->add('Point', $validator->errors());
            }
            else
            {
                $point_data                    = new \App\Models\PointLog;

                $point_data                    = $point_data->fill($point);

                if(!$point_data->save())
                {
                    $errors->add('Point', $point_data->getError());
                }
            }
        }

        if($errors->count())
        {
            DB::rollback();

            return new JSend('error', (array)Input::all(), $errors);
        }

        DB::commit();
        
        $final_point                 = \App\Models\PointLog::id($point_data['id'])->with(['user'])->first();

        return new JSend('success', (array)$final_point);
    }
}
