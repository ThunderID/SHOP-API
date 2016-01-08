<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;

/**
 * Handle Protected Warehouse's report
 * 
 * @author cmooy
 */
class WarehouseController extends Controller
{
    /**
     * Display product stock's movement
     *
     * @param skip, take
     * @return Response
     */
    public function card($id = null)
    {
        $result                     = \App\Models\TransactionDetail::varianid($id)->stockmovement(true);

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

        $result                     = $result->with(['varian', 'varian.product'])->get()->toArray();

        return new JSend('success', (array)['count' => $count, 'data' => $result]);
    }

    /**
     * Display products critical stock
     *
     * @param skip, take
     * @return Response
     */
    public function critical()
    {
        $setting                    = \App\Models\Policy::ondate('now')->type('critical_stock')->first();

        if(!$setting)
        {
            $critical               = 0;
        }
        else
        {
            $critical               = $setting['value'];
        }

        $result                     = \App\Models\Varian::critical($critical);
        
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

        $result                     = $result->with(['product'])->get()->toArray();

        return new JSend('success', (array)['count' => $count, 'data' => $result]);
    }

    /**
     * Display products critical stock
     *
     * @param skip, take
     * @return Response
     */
    public function opname()
    {
        $result                     = \App\Models\Varian::with(['product']);
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

        $result                     = $result->get()->toArray();

        return new JSend('success', (array)['count' => $count, 'data' => $result]);
    }
}
