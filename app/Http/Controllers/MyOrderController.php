<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MyOrderController extends Controller
{
    /**
     * Display all customer's recorded orders
     *
     * @return Response
     */
    public function index($id = null)
    {
        $result                 = \App\Models\Sale::userid($id)->status(['wait', 'canceled', 'paid', 'shipping', 'packed', 'delivered'])->get()->toArray();

        return new JSend('success', (array)$result);
    }

    /**
     * Display an order by customer
     *
     * @return Response
     */
    public function detail($id = null, $order_id = null)
    {
        $result                 = \App\Models\Sale::userid($id)->id($order_id)->status(['wait', 'canceled', 'paid', 'shipping', 'packed', 'delivered'])->first()->toArray();

        return new JSend('success', (array)$result);
    }

    /**
     * Display an order from customer's cart
     *
     * @return Response
     */
    public function incart($id = null, $order_id = null)
    {
        $result                 = \App\Models\Sale::userid($id)->id($order_id)->status('cart')->first();

        return new JSend('success', (array)$result);
    }
}
