<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;

/**
 * Handle Protected reports
 * 
 * @author cmooy
 */
class ReportController extends Controller
{
    /**
     * Display product stock's movement
     *
     * @param skip, take
     * @return Response
     */
    public function voucher()
    {
        $result                     = new \App\Models\Sale;

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

        $result                     = $result->with(['user', 'transactiondetails', 'transactiondetails.varian', 'transactiondetails.varian.product', 'payment', 'paidpointlogs', 'paidpointlogs.referencepointvoucher', 'paidpointlogs.referencepointvoucher.referencevoucher', 'paidpointlogs.referencepointreferral', 'paidpointlogs.referencepointreferral.referencereferral'])->get()->toArray();

        return new JSend('success', (array)['count' => $count, 'data' => $result]);
    }
}
