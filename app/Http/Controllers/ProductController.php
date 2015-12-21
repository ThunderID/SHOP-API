<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        $result                     = \App\Models\Product::groupby('products.id')->with(['varians'])->get()->toArray();
        dd($result);
        return new JSend('success', (array)$result);
    }
}
