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
class UIController extends Controller
{
    /**
     * Display all sellable products
     *
     * @return Response
     */
    public function products()
    {
        $result                     = new \App\Models\Product;

        if(Input::has('search'))
        {
            $search                 = Input::get('search');

            foreach ($search as $key => $value) 
            {
                switch (strtolower($key)) 
                {
                    case 'labelname':
                        $result     = $result->labelsname($value);
                        break;
                    
                    default:
                        # code...
                        break;
                }
            }
        }

        $result                     = $result->sellable(true);

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

        $result                     = $result->with(['varians', 'images', 'labels'])->get()->toArray();

        return new JSend('success', (array)['count' => $count, 'data' => $result]);
    }

    /**
     * Display all sellable clusters
     *
     * @return Response
     */
    public function clusters($type = null)
    {
        if($type=='category')
        {
            $result                 = \App\Models\Category::orderby('path', 'asc')->with(['category']);
        }
        else
        {
            $result                 = \App\Models\Tag::orderby('path', 'asc')->with(['tag']);
        }

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

        $result                     = $result->get()->toArray();

        return new JSend('success', (array)['count' => $count, 'data' => $result]);
    }
}
