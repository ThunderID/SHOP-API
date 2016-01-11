<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * Handle Protected Resource of customer
 * 
 * @author cmooy
 */
class CustomerController extends Controller
{
    /**
     * Display all customers
     *
     * @return Response
     */
    public function index()
    {
        $result                 = \App\Models\Customer::get()->toArray();

        return new JSend('success', (array)$result);
    }

    /**
     * Display a customer
     *
     * @return Response
     */
    public function detail($id = null)
    {
        $result                 = \App\Models\Customer::id($id)->with(['sales'])->first()->toArray();

        return new JSend('success', (array)$result);
    }
}
