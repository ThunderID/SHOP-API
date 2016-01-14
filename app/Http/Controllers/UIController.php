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
                    case 'name':
                        $result     = $result->name($value);
                        break;
                    case 'slug':
                        $result     = $result->slug($value);
                        break;
                    case 'categories':
                        $result     = $result->categoriesslug($value);
                        break;
                    case 'tags':
                        $result     = $result->tagsslug($value);
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }

        $result                     = $result->sellable(true);

        if(Input::has('sort'))
        {
            $sort                 = Input::get('sort');

            foreach ($sort as $key => $value) 
            {
                if(!in_array($value, ['asc', 'desc']))
                {
                    return new JSend('error', (array)Input::all(), $key.' harus bernilai asc atau desc.');
                }
                switch (strtolower($key)) 
                {
                    case 'name':
                        $result     = $result->orderby($value, $key);
                        break;
                    case 'price':
                        $result     = $result->orderby($value, $key);
                        break;
                    case 'newest':
                        $result     = $result->orderby('created_at', $key);
                        break;
                    
                    default:
                        # code...
                        break;
                }
            }
        }

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
