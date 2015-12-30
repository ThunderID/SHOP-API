<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

use App\Models\Cluster;

/* ----------------------------------------------------------------------
 * Event:
 * saving
 * deleting
 * ---------------------------------------------------------------------- */

class ClusterObserver 
{
    public function created($model)
    {
        if($model->category()->count())
        {
            $model->path           = $model->category->path.','.$model->id;
        }
        else
        {
            $model->path           = $model->id;
        }

        if(!$model->save())
        {
            $model['errors']            = $model->getError();

            return false;
        }

        return true;
    }

    public function saving($model)
    {
        if(isset($model->category_id) && $model->category_id != 0 )
        {
            $model->slug                = Str::slug($model->category->name.' '.$model->name);
        }
        else
        {
            $model->slug                = Str::slug($model->name);
        }

        if(is_null($model->id))
        {
            $id                         = 0;
        }
        else
        {
            $id                         = $model->id;
        }

        $category                       = Cluster::slug($model->slug)->notid($id)->first();

        if($category)
        {
            $model['errors']            = 'Kategori/tag sudah terdaftar.';
        
            return false;
        }

        return true;
    }

    public function updating($model)
    {
        if(isset($model->getDirty()['category_id']) || !isset($model ->getDirty()['path']))
        {
            if($model->category()->count())
            {
                $model->path = $model->category->path . "," . $model ->id;
            }
            else
            {
                $model->path = $model->id;
            }

            if(isset($model ->getOriginal()['path']))
            {
                //mengganti semua path child
                $childs                         = Cluster::orderBy('path','asc')
                                                    ->where('path','like',$model->getOriginal()['path'] . ',%')
                                                    ->get();

                foreach ($childs as $child) 
                {
                    $child->update(['path' => preg_replace('/'. $model ->getOriginal()['path'].',/', $model ->path . ',', $child->path,1)]);  
                }
            }
        }

        return true;
    }

    public function deleting($model)
    {
		$errors 						= new MessageBag();

		/* --------------------------------DELETE VARIAN RELATIONSHIP--------------------------------------*/

        //1. Check varian relationship with transaction
        if($model->transactions()->count())
        {
            $errors->add('varian', 'Tidak dapat menghapus produk varian yang pernah di stok &/ order.');
        }

        if($errors->count())
        {
			$model['errors'] 		= $errors;

        	return false;
        }

        return true;
    }
}
