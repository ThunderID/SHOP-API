<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ClusterController extends Controller
{
    /**
     * Display all products
     *
     * @return Response
     */
    public function index()
    {
        if(Input::has('type') && Input::get('type')=='category')
        {
            $result                 = \App\Models\Category::orderby('path', 'asc')->with(['category'])->get()->toArray();
        }
        else
        {
            $result                 = \App\Models\Tag::orderby('path', 'asc')->with(['tag'])->get()->toArray();
        }

        return new JSend('success', (array)$result);
    }

    /**
     * Display a product
     *
     * @return Response
     */
    public function detail($id = null)
    {
        if(Input::has('type') && Input::get('type')=='category')
        {
            $result                 = \App\Models\Category::id($id)->orderby('path', 'asc')->with(['category', 'products'])->first()->toArray();
        }
        else
        {
            $result                 = \App\Models\Tag::id($id)->orderby('path', 'asc')->with(['tag', 'products'])->first()->toArray();
        }

        return new JSend('success', (array)$result);
    }

    /**
     * Store a product
     *
     * @return Response
     */
    public function store()
    {
        if(!Input::has('cluster'))
        {
            return new JSend('error', (array)Input::all(), 'Tidak ada data cluster.');
        }

        $errors                     = new MessageBag();

        DB::beginTransaction();

        //1. Validate Cluster Parameter

        // $cluster                    = Input::get('cluster');
        if(is_null($cluster['id']))
        {
            $is_new                 = true;
        }
        else
        {
            $is_new                 = false;
        }

        $cluster_rules             =   [
                                            'category_id'               => 'numeric',
                                            'type'                      => 'required|in:category,id',
                                            'path'                      => 'required|max:255',
                                            'name'                      => 'required|max:255',
                                            'slug'                      => 'required|max:255|unique:categories,slug,'.(!is_null($cluster['id']) ? $cluster['id'] : ''),
                                        ];

        //1a. Get original data
        $cluster_data              = \App\Models\Cluster::findornew($cluster['id']);

        //1b. Validate Basic Cluster Parameter
        $validator                  = Validator::make($cluster, $cluster_rules);

        if (!$validator->passes())
        {
            $errors->add('Cluster', $validator->errors());
        }
        else
        {
            //if validator passed, save cluster
            $cluster_data           = $cluster_data->fill($cluster);

            if(!$cluster_data->save())
            {
                $errors->add('Cluster', $cluster_data->getError());
            }
        }
        //End of validate cluster


        if($errors->count())
        {
            DB::rollback();

            return new JSend('error', (array)Input::all(), $errors);
        }

        DB::commit();
        
        $final_cluster              = \App\Models\Cluster::id($cluster_data['id'])->with(['category', 'products'])->first()->toArray();

        return new JSend('success', (array)$final_cluster);
    }

    /**
     * Delete a product
     *
     * @return Response
     */
    public function delete($id = null)
    {
        //
        if(Input::has('type') && Input::get('type')=='category')
        {
            $cluster                = \App\Models\Category::id($id)->orderby('path', 'asc')->with(['category', 'products'])->first();
        }
        else
        {
            $cluster                = \App\Models\Tag::id($id)->orderby('path', 'asc')->with(['tag', 'products'])->first();
        }

        $result                     = $cluster;

        if($cluster->delete())
        {
            return new JSend('success', (array)$result);
        }

        return new JSend('error', (array)$result, $cluster->getError());
    }
}
