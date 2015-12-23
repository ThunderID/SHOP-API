<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;

class WarehouseController extends Controller
{
    /**
     * Display product stock
     *
     * @return Response
     */
    public function card($id = null)
    {
        $result                                 = \App\Models\TransactionDetail::stockmovement(true)->varianid($id)->get()->toArray();

        return new JSend('success', (array)$result);
    }

    /**
     * Display products critical stock
     *
     * @return Response
     */
    public function critical()
    {
        $setting                                = \App\Models\Policy::ondate('now')->type('critical_stock')->first();

        if(!$setting)
        {
            $critical                           = 0;
        }
        else
        {
            $critical                           = 0 - $setting['value'];
        }

        $result                                 = \App\Models\Varian::critical($critical)->with(['product'])->get()->toArray();

        return new JSend('success', (array)$result);
    }

    /**
     * Display products opname stock
     *
     * @return Response
     */
    public function opname()
    {
        $result                                 = \App\Models\Varian::with(['product'])->get()->toArray();

        return new JSend('success', (array)$result);
    }
}