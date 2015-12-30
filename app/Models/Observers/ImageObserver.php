<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

use App\Models\Image;

/* ----------------------------------------------------------------------
 * Event:
 * saving
 * deleting
 * ---------------------------------------------------------------------- */

class ImageObserver 
{
	public function saving($model)
    {
		if(isset($model->imageable_id))
        {
            $model                     = Image::where('imageable_id', $model->imageable_id)
                                            ->where('imageable_type', $model->imageable_type)
                                            ->where('is_default', 1)
                                            ->count();
            if($model == 0)
            {
                $model->is_default     = 1;
            }
        }

        return true;
    }

    public function saved($model)
    {
		if(isset($model->imageable_id) && $model->is_default == 1)
        {
            $images                     = Image::where('imageable_id', $model->imageable_id)
                                            ->where('imageable_type', $model->imageable_type)
                                            ->where('is_default', 1)
                                            ->where('id','!=', $model->id)
                                            ->get();

            foreach ($images as $image) 
            {
               $image->fill([
                    'is_default'        => 0,
                ]);

               if(!$image->save())
               {
                    $model['errors']    = $image->geterror();

                    return false;
               }
            }
        }

        return true;
    }
}
