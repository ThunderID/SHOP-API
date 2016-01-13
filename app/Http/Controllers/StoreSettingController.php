<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * Handle Protected display and store of setting, there were 4 type of setting, there are slider, page, store, and policy
 * 
 * @author cmooy
 */
class StoreSettingController extends Controller
{
    /**
     * Display all settings
     *
     * @param type, search, skip, take
     * @return Response
     */
    public function index($type = null)
    {
        $result                 = new \App\Models\StoreSetting;

        switch (strtolower($type)) 
        {
            case 'slider':
                $result         = \App\Models\Slider::with(['images']);
                break;
            case 'page':
                $result         = new \App\Models\StorePage;
                break;
            case 'store':
                $result         = new \App\Models\Store;
                break;
            case 'policy':
                $result         = new \App\Models\Policy;
                break;
        }

        if(Input::has('search'))
        {
            $search                 = Input::get('search');

            foreach ($search as $key => $value) 
            {
                switch (strtolower($key)) 
                {
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

    /**
     * Display a setting
     *
     * @return Response
     */
    public function detail($id = null)
    {
        $result                 = \App\Models\StoreSetting::id($id)->first();

        if($result)
        {
            if($result['type']=='slider')
            {
                $result         = \App\Models\Slider::id($id)->with(['images'])->first();
            }

            return new JSend('success', (array)$result->toArray());
        }

        return new JSend('error', (array)Input::all(), 'ID Tidak Valid.');
    }

    /**
     * Store a setting
     *
     * @return Response
     */
    public function store()
    {
        if(!Input::has('setting'))
        {
            return new JSend('error', (array)Input::all(), 'Tidak ada data setting.');
        }

        $errors                     = new MessageBag();

        DB::beginTransaction();

        //1. Validate StoreSetting Parameter
        $setting                    = Input::get('setting');
        
        if(is_null($setting['id']))
        {
            $is_new                 = true;
        }
        else
        {
            $is_new                 = false;
        }

        //2. Validate setting parameter
        //2a. Slider 
        if(!$errors->count() && $setting['type'] == 'slider')
        {
            $setting_data           = \App\Models\Slider::findornew($setting['id']);

            $setting_rules          =   [
                                                'started_at'                => 'date_format:"Y-m-d H:i:s"',
                                                'ended_at'                  => 'date_format:"Y-m-d H:i:s"',
                                            ];

            $validator              = Validator::make($setting, $setting_rules);
        }
        //2b. Page 
        elseif(!$errors->count() && in_array($setting['type'], ['about_us', 'why_join', 'term_and_condition']))
        {
            $setting_data           = \App\Models\StorePage::findornew($setting['id']);

            $setting_rules          =   [
                                                'started_at'                => 'date_format:"Y-m-d H:i:s"',
                                            ];

            $validator              = Validator::make($setting, $setting_rules);
        }
        //2c. Store 
        elseif(!$errors->count() && in_array($setting['type'], ['url', 'logo', 'facebook_url', 'twitter_url', 'instagram_url', 'email', 'phone', 'address', 'bank_information']))
        {
            $setting_data           = \App\Models\Store::findornew($setting['id']);

            $setting_rules          =   [
                                                'started_at'                => 'date_format:"Y-m-d H:i:s"',
                                            ];

            $validator              = Validator::make($setting, $setting_rules);
        }
        //2d. Policy 
        else
        {
            $setting_data           = \App\Models\Policy::findornew($setting['id']);

            $setting_rules          =       [
                                                'started_at'                => 'date_format:"Y-m-d H:i:s"',
                                            ];

            $validator              = Validator::make($setting, $setting_rules);
        }

        if (!$validator->passes())
        {
            $errors->add('StoreSetting', $validator->errors());
        }
        else
        {
            //if validator passed, save setting
            $setting_data           = $setting_data->fill($setting);

            if(!$setting_data->save())
            {
                $errors->add('StoreSetting', $setting_data->getError());
            }
        }

        //3. save image for slider
        if(!$errors->count() && isset($setting['images']) && is_array($setting['images']) && $setting_data['type']=='slider')
        {
            $image_current_ids         = [];
            foreach ($setting['images'] as $key => $value) 
            {
                if(!$errors->count())
                {
                    $image_data        = \App\Models\Image::find($value['id']);

                    if($image_data)
                    {
                        $image_rules   =   [
                                                'imageable_id'              => 'required|numeric|'.($is_new ? '' : 'in:'.$setting_data['id']),
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
                                                'imageable_id'              => 'numeric|'.($is_new ? '' : 'in:'.$setting_data['id']),
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
                        if($value['imageable_id']!=$setting['id'])
                        {
                            $errors->add('Image', 'Image slider tidak Valid.');
                        }
                        elseif($is_new)
                        {
                            $errors->add('Image', 'Image slider tidak Valid.');
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
                        $value['imageable_id']          = $setting_data['id'];
                        $value['imageable_type']        = get_class($setting_data);

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
                    $images                            = \App\Models\Image::imageableid($setting['id'])->get()->toArray();
                    
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

        if($errors->count())
        {
            DB::rollback();

            return new JSend('error', (array)Input::all(), $errors);
        }

        DB::commit();
        
        if($setting_data['type']=='slider')
        {
            $final_setting          = \App\Models\Slider::id($setting_data['id'])->with(['images'])->first()->toArray();
        }
        else
        {
            $final_setting          = \App\Models\StoreSetting::id($setting_data['id'])->first()->toArray();
        }

        return new JSend('success', (array)$final_setting);
    }
}
