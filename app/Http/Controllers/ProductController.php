<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display all products
     *
     * @return Response
     */
    public function index()
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

        $result                     = $result->with(['varians'])->get()->toArray();

        return new JSend('success', (array)$result);
    }

    /**
     * Display a product
     *
     * @return Response
     */
    public function detail($id = null)
    {
        //
        $result                     = \App\Models\Product::id($id)->with(['varians', 'categories', 'tags', 'labels', 'images', 'prices'])->first();

        if($result)
        {
            return new JSend('success', (array)$result->toArray());

        }
        return new JSend('error', (array)Input::all(), 'ID Tidak Valid.');
    }

    /**
     * Store a product
     *
     * @return Response
     */
    public function store()
    {
        if(!Input::has('product'))
        {
            return new JSend('error', (array)Input::all(), 'Tidak ada data product.');
        }

        $errors                     = new MessageBag();

        DB::beginTransaction();

        //1. Validate Product Parameter

        // $product                    = Input::get('product');
        if(is_null($product['id']))
        {
            $is_new                 = true;
        }
        else
        {
            $is_new                 = false;
        }

        $product['description']     = json_decode($product['description'], true);

        $product_rules              =   [
                                            'name'                      => 'required|max:255',
                                            'upc'                       => 'required|max:255|unique:products,upc,'.(!is_null($product['id']) ? $product['id'] : ''),
                                            'slug'                      => 'required|max:255|unique:products,slug,'.(!is_null($product['id']) ? $product['id'] : ''),
                                            'description.description'   => 'required|max:512',
                                            'description.fit'           => 'required|max:512',
                                        ];

        //1a. Get original data
        $product_data               = \App\Models\Product::findornew($product['id']);

        //1b. Validate Basic Product Parameter
        $validator                  = Validator::make($product, $product_rules);

        if (!$validator->passes())
        {
            $errors->add('Product', $validator->errors());
        }
        else
        {
            //if validator passed, save product
            $product['description'] = json_encode($product['description']);

            $product_data           = $product_data->fill($product);

            if(!$product_data->save())
            {
                $errors->add('Product', $product_data->getError());
            }
        }
        //End of validate product

        //2. Validate Product Varian Parameter
        if(!$errors->count())
        {
            $varian_current_ids         = [];
            foreach ($product['varians'] as $key => $value) 
            {
                if(!$errors->count())
                {
                    $varian_data        = \App\Models\Varian::find($value['id']);

                    if($varian_data)
                    {
                        $varian_rules   =   [
                                                'product_id'                => 'required|numeric|'.($is_new ? '' : 'in:'.$product_data['id']),
                                                'sku'                       => 'required|max:255|in:'.$varian_data['sku'].'unique:varians,sku,'.(!is_null($varian_data['id']) ? $varian_data['id'] : ''),
                                                'size'                      => 'required|max:255|in:'.$varian_data['size'],
                                            ];

                        $validator      = Validator::make($varian_data['attributes'], $varian_rules);
                    }
                    else
                    {
                        $varian_rules   =   [
                                                'product_id'                => 'numeric|'.($is_new ? '' : 'in:'.$product_data['id']),
                                                'sku'                       => 'required|max:255|unique:varians,sku,'.(!is_null($value['id']) ? $value['id'] : ''),
                                                'size'                      => 'required|max:255|',
                                            ];

                        $validator      = Validator::make($value, $varian_rules);
                    }

                    //if there was varian and validator false
                    if ($varian_data && !$validator->passes())
                    {
                        if($value['product_id']!=$product['id'])
                        {
                            $errors->add('Varian', 'Produk dari Varian Tidak Valid.');
                        }
                        elseif($is_new)
                        {
                            $errors->add('Varian', 'Produk Varian Tidak Valid.');
                        }
                        else
                        {
                            $varian_data                = $varian_data->fill($value);

                            if(!$varian_data->save())
                            {
                                $errors->add('Varian', $varian_data->getError());
                            }
                            else
                            {
                                $varian_current_ids[]   = $varian_data['id'];
                            }
                        }
                    }
                    //if there was varian and validator false
                    elseif (!$varian_data && !$validator->passes())
                    {
                        $errors->add('Varian', $validator->errors());
                    }
                    elseif($varian_data && $validator->passes())
                    {
                        $varian_current_ids[]           = $varian_data['id'];
                    }
                    else
                    {
                        $value['product_id']            = $product_data['id'];

                        $varian_data                    = new \App\Models\Varian;

                        $varian_data                    = $varian_data->fill($value);

                        if(!$varian_data->save())
                        {
                            $errors->add('Varian', $varian_data->getError());
                        }
                        else
                        {
                            $varian_current_ids[]       = $varian_data['id'];
                        }
                    }
                }

                //if there was no error, check if there were things need to be delete
                if(!$errors->count())
                {
                    $varians                            = \App\Models\Varian::productid($product['id'])->get()->toArray();
                    
                    $varian_should_be_ids               = [];
                    foreach ($varians as $key => $value) 
                    {
                        $varian_should_be_ids[]         = $value['id'];
                    }

                    $difference_varian_ids              = array_diff($varian_should_be_ids, $varian_current_ids);

                    if($difference_varian_ids)
                    {
                        foreach ($difference_varian_ids as $key => $value) 
                        {
                            $varian_data                = \App\Models\Varian::find($value);

                            if(!$varian_data->delete())
                            {
                                $errors->add('Varian', $varian_data->getError());
                            }
                        }
                    }
                }
            }
        }

        //End of validate product varian

        //3. Validate Product Price Parameter
        if(!$errors->count())
        {
            $price_current_ids         = [];
            foreach ($product['prices'] as $key => $value) 
            {
                if(!$errors->count())
                {
                    $price_data        = \App\Models\Price::find($value['id']);

                    if($price_data)
                    {
                        $price_rules   =   [
                                                'product_id'                => 'required|numeric|'.($is_new ? '' : 'in:'.$product_data['id']),
                                                'price'                     => 'required|numeric|in:'.$price_data['price'],
                                                'promo_price'               => 'required|numeric|in:'.$price_data['promo_price'],
                                                'started_at'                => 'required|date_format:"Y-m-d H:i:s"',
                                            ];

                        $validator      = Validator::make($price_data['attributes'], $price_rules);
                    }
                    else
                    {
                        $price_rules   =   [
                                                'product_id'                => 'numeric|'.($is_new ? '' : 'in:'.$product_data['id']),
                                                'price'                     => 'required|numeric',
                                                'promo_price'               => 'required|numeric',
                                                'started_at'                => 'required|date_format:"Y-m-d H:i:s"',
                                            ];

                        $validator      = Validator::make($value, $price_rules);
                    }

                    //if there was price and validator false
                    if ($price_data && !$validator->passes())
                    {
                        if($value['product_id']!=$product['id'])
                        {
                            $errors->add('Price', 'Produk dari Price Tidak Valid.');
                        }
                        elseif($is_new)
                        {
                            $errors->add('Price', 'Produk Price Tidak Valid.');
                        }
                        else
                        {
                            $price_data                = $price_data->fill($value);

                            if(!$price_data->save())
                            {
                                $errors->add('Price', $price_data->getError());
                            }
                            else
                            {
                                $price_current_ids[]   = $price_data['id'];
                            }
                        }
                    }
                    //if there was price and validator false
                    elseif (!$price_data && !$validator->passes())
                    {
                        $errors->add('Price', $validator->errors());
                    }
                    elseif($price_data && $validator->passes())
                    {
                        $price_current_ids[]           = $price_data['id'];
                    }
                    else
                    {
                        $value['product_id']           = $product_data['id'];

                        $price_data                    = new \App\Models\Price;

                        $price_data                    = $price_data->fill($value);

                        if(!$price_data->save())
                        {
                            $errors->add('Price', $price_data->getError());
                        }
                        else
                        {
                            $price_current_ids[]       = $price_data['id'];
                        }
                    }
                }

                //if there was no error, check if there were things need to be delete
                if(!$errors->count())
                {
                    $prices                            = \App\Models\Price::productid($product['id'])->get()->toArray();
                    
                    $price_should_be_ids               = [];
                    foreach ($prices as $key => $value) 
                    {
                        $price_should_be_ids[]         = $value['id'];
                    }

                    $difference_price_ids              = array_diff($price_should_be_ids, $price_current_ids);

                    if($difference_price_ids)
                    {
                        foreach ($difference_price_ids as $key => $value) 
                        {
                            $price_data                = \App\Models\Price::find($value);

                            if(!$price_data->delete())
                            {
                                $errors->add('Price', $price_data->getError());
                            }
                        }
                    }
                }
            }
        }
        //End of validate product price

        //4. Validate Product Category Parameter
        if(!$errors->count())
        {
            $category_current_ids               = [];

            foreach ($product['categories'] as $key => $value) 
            {
                $category                       = \App\Models\Category::find($value['id']);

                if($category)
                {
                    $category_current_ids[]     = $value['id'];
                }
                else
                {
                    $errors->add('Category', 'Kategori tidak valid.');
                }
            }

            if($errors->count())
            {
                if(!$product_data->categories()->sync($category_current_ids))
                {
                    $errors->add('Category', 'Kategori produk tidak tersimpan.');
                }
            }
        }
        //End of validate product category

        //5. Validate Product Tag Parameter
        if(!$errors->count())
        {
            $tag_current_ids                = [];

            foreach ($product['tags'] as $key => $value) 
            {
                $tag                        = \App\Models\Tag::find($value['id']);

                if($tag)
                {
                    $tag_current_ids[]      = $value['id'];
                }
                else
                {
                    $errors->add('Tag', 'Tag tidak valid.');
                }
            }

            if($errors->count())
            {
                if(!$product_data->categories()->sync($tag_current_ids))
                {
                    $errors->add('Tag', 'Tag produk tidak tersimpan.');
                }
            }
        }
        //End of validate product category

        //6. Validate Product Label Parameter
        if(!$errors->count())
        {
            $label_current_ids         = [];
            foreach ($product['labels'] as $key => $value) 
            {
                if(!$errors->count())
                {
                    $label_data        = \App\Models\ProductLabel::find($value['id']);

                    if($label_data)
                    {
                        $label_rules   =   [
                                                'product_id'                => 'required|numeric|'.($is_new ? '' : 'in:'.$product_data['id']),
                                                'lable'                     => 'required|max:255|in:'.$label_data['lable'],
                                                'value'                     => 'required|in:'.$label_data['value'],
                                                'started_at'                => 'required|date_format:"Y-m-d H:i:s"in:'.$label_data['started_at'],
                                                'ended_at'                  => 'date_format:"Y-m-d H:i:s"|in:'.$label_data['ended_at'],
                                            ];

                        $validator      = Validator::make($label_data['attributes'], $label_rules);
                    }
                    else
                    {
                        $label_rules   =   [
                                                'product_id'                => 'numeric|'.($is_new ? '' : 'in:'.$product_data['id']),
                                                'lable'                     => 'required|max:255',
                                                'value'                     => 'required',
                                                'started_at'                => 'required|date_format:"Y-m-d H:i:s"',
                                                'ended_at'                  => 'date_format:"Y-m-d H:i:s"',
                                            ];

                        $validator      = Validator::make($value, $label_rules);
                    }

                    //if there was label and validator false
                    if ($label_data && !$validator->passes())
                    {
                        if($value['product_id']!=$product['id'])
                        {
                            $errors->add('ProductLabel', 'Produk dari ProductLabel Tidak Valid.');
                        }
                        elseif($is_new)
                        {
                            $errors->add('ProductLabel', 'Produk ProductLabel Tidak Valid.');
                        }
                        else
                        {
                            $label_data                = $label_data->fill($value);

                            if(!$label_data->save())
                            {
                                $errors->add('ProductLabel', $label_data->getError());
                            }
                            else
                            {
                                $label_current_ids[]   = $label_data['id'];
                            }
                        }
                    }
                    //if there was label and validator false
                    elseif (!$label_data && !$validator->passes())
                    {
                        $errors->add('ProductLabel', $validator->errors());
                    }
                    elseif($label_data && $validator->passes())
                    {
                        $label_current_ids[]           = $label_data['id'];
                    }
                    else
                    {
                        $value['product_id']            = $product_data['id'];

                        $label_data                    = new \App\Models\ProductLabel;

                        $label_data                    = $label_data->fill($value);

                        if(!$label_data->save())
                        {
                            $errors->add('ProductLabel', $label_data->getError());
                        }
                        else
                        {
                            $label_current_ids[]       = $label_data['id'];
                        }
                    }
                }

                //if there was no error, check if there were things need to be delete
                if(!$errors->count())
                {
                    $labels                            = \App\Models\ProductLabel::productid($product['id'])->get()->toArray();
                    
                    $label_should_be_ids               = [];
                    foreach ($labels as $key => $value) 
                    {
                        $label_should_be_ids[]         = $value['id'];
                    }

                    $difference_label_ids              = array_diff($label_should_be_ids, $label_current_ids);

                    if($difference_label_ids)
                    {
                        foreach ($difference_label_ids as $key => $value) 
                        {
                            $label_data                = \App\Models\ProductLabel::find($value);

                            if(!$label_data->delete())
                            {
                                $errors->add('ProductLabel', $label_data->getError());
                            }
                        }
                    }
                }
            }
        }
        //End of validate product label

        //7. Validate Product Image Parameter
        if(!$errors->count())
        {
            $label_current_ids         = [];
            foreach ($product['images'] as $key => $value) 
            {
                if(!$errors->count())
                {
                    $image_data        = \App\Models\Image::find($value['id']);

                    if($image_data)
                    {
                        $image_rules   =   [
                                                'imageable_id'              => 'required|numeric|'.($is_new ? '' : 'in:'.$product_data['id']),
                                                'imageable_type'            => 'required|max:255|in:'.$image_data['imageable_type'],
                                                'thumbnail'                 => 'required|max:255|in:'.$image_data['thumbnail'],
                                                'image_xs'                  => 'required|max:255|in:'.$image_data['image_xs'],
                                                'image_sm'                  => 'required|max:255|in:'.$image_data['image_sm'],
                                                'image_md'                  => 'required|max:255|in:'.$image_data['image_md'],
                                                'image_lg'                  => 'required|max:255|in:'.$image_data['image_lg'],
                                                'is_default'                => 'boolean|in:'.$image_data['is_default'],
                                            ];

                        $validator      = Validator::make($image_data['attributes'], $image_rules);
                    }
                    else
                    {
                        $image_rules   =   [
                                                'imageable_id'              => 'numeric|'.($is_new ? '' : 'in:'.$product_data['id']),
                                                'thumbnail'                 => 'required|max:255',
                                                'image_xs'                  => 'required|max:255',
                                                'image_sm'                  => 'required|max:255',
                                                'image_md'                  => 'required|max:255',
                                                'image_lg'                  => 'required|max:255',
                                                'is_default'                => 'boolean',
                                            ];

                        $validator      = Validator::make($value, $image_rules);
                    }

                    //if there was image and validator false
                    if ($image_data && !$validator->passes())
                    {
                        if($value['imageable_id']!=$product['id'])
                        {
                            $errors->add('Image', 'Produk dari Image Tidak Valid.');
                        }
                        elseif($is_new)
                        {
                            $errors->add('Image', 'Produk Image Tidak Valid.');
                        }
                        else
                        {
                            $image_data                = $image_data->fill($value);

                            if(!$image_data->save())
                            {
                                $errors->add('Image', $image_data->getError());
                            }
                            else
                            {
                                $image_current_ids[]   = $image_data['id'];
                            }
                        }
                    }
                    //if there was image and validator false
                    elseif (!$image_data && !$validator->passes())
                    {
                        $errors->add('Image', $validator->errors());
                    }
                    elseif($image_data && $validator->passes())
                    {
                        $image_current_ids[]            = $image_data['id'];
                    }
                    else
                    {
                        $value['imageable_id']          = $product_data['id'];
                        $value['imageable_type']        = get_class($product_data);

                        $image_data                     = new \App\Models\Image;

                        $image_data                     = $image_data->fill($value);

                        if(!$image_data->save())
                        {
                            $errors->add('Image', $image_data->getError());
                        }
                        else
                        {
                            $image_current_ids[]       = $image_data['id'];
                        }
                    }
                }

                //if there was no error, check if there were things need to be delete
                if(!$errors->count())
                {
                    $images                            = \App\Models\Image::imageableid($product['id'])->get()->toArray();
                    
                    $image_should_be_ids               = [];
                    foreach ($images as $key => $value) 
                    {
                        $image_should_be_ids[]         = $value['id'];
                    }

                    $difference_image_ids              = array_diff($image_should_be_ids, $image_current_ids);

                    if($difference_image_ids)
                    {
                        foreach ($difference_image_ids as $key => $value) 
                        {
                            $image_data                = \App\Models\Image::find($value);

                            if(!$image_data->delete())
                            {
                                $errors->add('Image', $image_data->getError());
                            }
                        }
                    }
                }
            }
        }
        //End of validate product image

        if($errors->count())
        {
            DB::rollback();

            return new JSend('error', (array)Input::all(), $errors);
        }

        DB::commit();
        
        $final_product              = \App\Models\Product::id($product_data['id'])->with(['varians', 'categories', 'tags', 'labels', 'images', 'prices'])->first()->toArray();

        return new JSend('success', (array)$final_product);
    }

    /**
     * Delete a product
     *
     * @return Response
     */
    public function delete($id = null)
    {
        //
        $product                    = \App\Models\Product::id($id)->with(['varians', 'categories', 'tags', 'labels', 'images', 'prices'])->first();

        if(!$product)
        {
            return new JSend('error', (array)Input::all(), 'Produk tidak ditemukan.');
        }

        $result                     = $product->toArray();

        if($product->delete())
        {
            return new JSend('success', (array)$result);
        }

        return new JSend('error', (array)$result, $product->getError());
    }
}
