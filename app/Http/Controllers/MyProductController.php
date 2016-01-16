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
    public function recommended($user_id = 0)
    {
        //1. Check tag/category viewed
        $stat                       = \App\Models\StatUserView::userid($user_id)->statabletype(['App\Models\Category', 'App\Models\Tag'])->get(['statable_id'])->toArray();

        //1b. Get slugs
        $slugs                      = [];
        $purchased_prods            = [];
        $purchased_varians          = [];
        foreach ($stat as $key => $value) 
        {
            $slugs[]                = \App\Models\Cluster::find($value['statable_id'])['slug'];
        }

        $purchased                  = \App\Models\TransactionDetail::TransactionSellOn(['paid', 'packed', 'shipping', 'delivered'])->where('transactions.user_id',$user_id)->with(['varian', 'varian.product', 'varian.product.clusters'])->get()->toArray();

        foreach ($purchased as $key => $value) 
        {
            //2. Check tag/category purchased
            foreach ($value['varian']['product']['clusters'] as $key2 => $value2) 
            {
                $slugs[]            = $value2['slug'];
            }

            $purchased_prods[]      = $value['varian']['product_id']; 
            $purchased_varians[]    = $value['varian']['size']; 
        }

        //2a. get slug of category/tag
        //2b. get product id
        //2c. get varian size
        $slug                       = array_unique($slugs);
        $productids                 = array_unique($purchased_prods);
        $variansize                 = array_unique($purchased_varians);

        $result                     = \App\Models\Product::sellable(true)->notid($productids)->variansize($variansize)->clustersslug($slug);

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
     * Display purchased product by customer
     *
     * @return Response
     */
    public function purchased($user_id = 0)
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
    public function viewed($user_id = 0)
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
