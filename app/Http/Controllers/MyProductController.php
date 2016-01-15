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
    public function recommended()
    {
        $result                     = new \App\Models\Varian;

        $count                      = count($result->get());

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
     * Display purchased product by customer
     *
     * @return Response
     */
    public function purchased()
    {
        $result                     = new \App\Models\Varian;

        $count                      = count($result->get());

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
     * Display viewed product by customer
     *
     * @return Response
     */
    public function viewed()
    {
        $result                     = new \App\Models\Varian;

        $count                      = count($result->get());

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
}
