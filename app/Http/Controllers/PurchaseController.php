<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /**
     * Display all products
     *
     * @return Response
     */
    public function index()
    {
        $result                 = \App\Models\Purchase::with(['transactionlogs', 'supplier', 'transactiondetails', 'transactiondetails.varian', 'transactiondetails.varian.product'])->get()->toArray();

        return new JSend('success', (array)$result);
    }

    /**
     * Display a product
     *
     * @return Response
     */
    public function detail($id = null)
    {
        $result                 = \App\Models\Purchase::id($id)->with(['transactionlogs', 'supplier', 'transactiondetails', 'transactiondetails.varian', 'transactiondetails.varian.product'])->first()->toArray();

        return new JSend('success', (array)$result);
    }

    /**
     * Store a product
     *
     * @return Response
     */
    public function store()
    {
        //1. Validate Purchase Parameter
        //2. Validate Purchase Detail Parameter
        //3. Validate Purchase Status Parameter
    }

    /**
     * Delete a product
     *
     * @return Response
     */
    public function delete($id = null)
    {
        //
        $purchase                   = \App\Models\Purchase::id($id)->with(['transactionlogs', 'supplier', 'transactiondetails', 'transactiondetails.varian', 'transactiondetails.varian.product'])->first();

        $result                     = $purchase;

        if($purchase->delete())
        {
            return new JSend('success', (array)$result);
        }

        return new JSend('error', (array)$result, $purchase->getError());
    }
}
