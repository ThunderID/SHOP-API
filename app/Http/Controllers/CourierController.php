<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * Handle Protected Resource of Courier
 * 
 * @author cmooy
 */
class CourierController extends Controller
{
    /**
     * Display all couriers
     *
     * @param search, skip, take
     * @return Response
     */
    public function index()
    {
        $result                 = new \App\Models\Courier;

        if(Input::has('search'))
        {
            $search                 = Input::get('search');

            foreach ($search as $key => $value) 
            {
                switch (strtolower($key)) 
                {
                    case 'name':
                        $result     = $result->name($value);
                        break;
                    
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

        $result                     = $result->with(['shippingcosts'])->get()->toArray();

        return new JSend('success', (array)['count' => $count, 'data' => $result]);
    }

    /**
     * Display a courier
     *
     * @return Response
     */
    public function cost($id = null)
    {
        $result                 = \App\Models\Courier::id($id)->with(['shippingcosts'])->first();
       
        if($result)
        {
            return new JSend('success', (array)$result->toArray());

        }
        return new JSend('error', (array)Input::all(), 'ID Tidak Valid.');

    }

    /**
     * Store a courier
     *
     * 1. Save Courier
     * 2. Save Shipping Cost
     * 
     * @return Response
     */
    public function store()
    {
        if(!Input::has('courier'))
        {
            return new JSend('error', (array)Input::all(), 'Tidak ada data courier.');
        }

        $errors                     = new MessageBag();

        DB::beginTransaction();

        //1. Validate Courier Parameter

        $courier                    = Input::get('courier');
        if(is_null($courier['id']))
        {
            $is_new                 = true;
        }
        else
        {
            $is_new                 = false;
        }

        $courier_rules             =   [
                                            'name'                      => 'required|max:255',
                                        ];

        //1a. Get original data
        $courier_data              = \App\Models\Courier::findornew($courier['id']);

        //1b. Validate Basic Courier Parameter
        $validator                  = Validator::make($courier, $courier_rules);

        if (!$validator->passes())
        {
            $errors->add('Courier', $validator->errors());
        }
        else
        {
            //if validator passed, save courier
            $courier_data           = $courier_data->fill($courier);

            if(!$courier_data->save())
            {
                $errors->add('Courier', $courier_data->getError());
            }
        }
        //End of validate courier

        //2. Validate Shipping Cost Parameter
        if(!$errors->count())
        {
            $cost_current_ids         = [];
            foreach ($courier['shippingcosts'] as $key => $value) 
            {
                if(!$errors->count())
                {
                    $cost_data        = \App\Models\ShippingCost::find($value['id']);

                    if($cost_data)
                    {
                        $cost_rules   =   [
                                                'courier_id'            => 'required|numeric|'.($is_new ? '' : 'in:'.$courier_data['courier_id']),
                                                'start_postal_code'     => 'required|max:255|in:'.$cost_data['start_postal_code'],
                                                'end_postal_code'       => 'required|max:255|in:'.$cost_data['end_postal_code'],
                                                'started_at'            => 'required|max:255|in:'.$cost_data['started_at'],
                                                'cost'                  => 'required|max:255|in:'.$cost_data['cost'],
                                            ];

                        $validator      = Validator::make($cost_data['attributes'], $cost_rules);
                    }
                    else
                    {
                        $cost_rules   =   [
                                                'courier_id'            => 'numeric|'.($is_new ? '' : 'in:'.$courier_data['courier_id']),
                                                'start_postal_code'     => 'required|max:255|',
                                                'end_postal_code'       => 'required|numeric|',
                                                'started_at'            => 'required|numeric|',
                                                'cost'                  => 'required|numeric|',
                                            ];

                        $validator      = Validator::make($value, $cost_rules);
                    }

                    //if there was cost and validator false
                    if ($cost_data && !$validator->passes())
                    {
                        if($value['courier_id']!=$courier['id'])
                        {
                            $errors->add('Cost', 'Shipping Cost dari Kurir Tidak Valid.');
                        }
                        elseif($is_new)
                        {
                            $errors->add('Cost', 'Shipping Cost dari Kurir Tidak Valid.');
                        }
                        else
                        {
                            $cost_data                = $cost_data->fill($value);

                            if(!$cost_data->save())
                            {
                                $errors->add('Cost', $cost_data->getError());
                            }
                            else
                            {
                                $cost_current_ids[]   = $cost_data['id'];
                            }
                        }
                    }
                    //if there was cost and validator false
                    elseif (!$cost_data && !$validator->passes())
                    {
                        $errors->add('Cost', $validator->errors());
                    }
                    elseif($cost_data && $validator->passes())
                    {
                        $cost_current_ids[]           = $cost_data['id'];
                    }
                    else
                    {
                        $value['courier_id']        = $courier_data['id'];

                        $cost_data                    = new \App\Models\ShippingCost;

                        $cost_data                    = $cost_data->fill($value);

                        if(!$cost_data->save())
                        {
                            $errors->add('Cost', $cost_data->getError());
                        }
                        else
                        {
                            $cost_current_ids[]       = $cost_data['id'];
                        }
                    }
                }
            }
            //if there was no error, check if there were things need to be delete
            if(!$errors->count())
            {
                $costs                            = \App\Models\ShippingCost::courierid($courier['id'])->get()->toArray();
                
                $cost_should_be_ids               = [];
                foreach ($costs as $key => $value) 
                {
                    $cost_should_be_ids[]         = $value['id'];
                }

                $difference_cost_ids              = array_diff($cost_should_be_ids, $cost_current_ids);

                if($difference_cost_ids)
                {
                    foreach ($difference_cost_ids as $key => $value) 
                    {
                        $cost_data                = \App\Models\ShippingCost::find($value);

                        if(!$cost_data->delete())
                        {
                            $errors->add('Cost', $cost_data->getError());
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
        
        $final_courier              = \App\Models\Courier::id($courier_data['id'])->with(['shippingcosts'])->first()->toArray();

        return new JSend('success', (array)$final_courier);
    }

    /**
     * Delete a courier
     *
     * @return Response
     */
    public function delete($id = null)
    {
        //
        $courier                    = \App\Models\Courier::id($id)->first();

        $result                     = $courier;

        if($courier->delete())
        {
            return new JSend('success', (array)$result);
        }

        return new JSend('error', (array)$result, $courier->getError());
    }
}
