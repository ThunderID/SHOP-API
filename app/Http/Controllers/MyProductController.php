<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MyProductController extends Controller
{
    /**
     * Display recommended product by customer
     *
     * @return Response
     */
    public function recommended($id = null)
    {
        $result                 = \App\Models\Product::get()->toArray();

        return new JSend('success', (array)$result);
    }

    /**
     * Display purchased product by customer
     *
     * @return Response
     */
    public function purchased($id = null)
    {
        $result                 = \App\Models\Product::get()->toArray();

        return new JSend('success', (array)$result);
    }

    /**
     * Display viewed product by customer
     *
     * @return Response
     */
    public function viewed($id = null)
    {
        $result                 = \App\Models\Product::get()->toArray();

        return new JSend('success', (array)$result);
    }
}
