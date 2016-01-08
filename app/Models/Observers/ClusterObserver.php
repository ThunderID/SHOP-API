<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

use App\Models\Cluster;

/**
 * Used in Cluster model
 *
 * @author cmooy
 */
class ClusterObserver 
{
    /** 
     * observe cluster event created
     * 1. modify path
     * 2. act, accept or refuse
     */
    public function created($model)
    {
        //1.modify path
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

    /** 
     * observe cluster event saving
     * 1. modify path
     * 2. check slug
     * 3. act, accept or refuse
     */
    public function saving($model)
    {
        //1.modify slug
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

        //2. check slug
        $category                       = Cluster::slug($model->slug)->notid($id)->first();

        if($category)
        {
            $model['errors']            = 'Kategori/tag sudah terdaftar.';
        
            return false;
        }

        return true;
    }

    /** 
     * observe cluster event updating
     * 1. updated parent + child path
     * 2. act, accept or refuse
     */
    public function updating($model)
    {
        //1. check parent
        if(isset($model->getDirty()['category_id']) || !isset($model ->getDirty()['path']))
        {
            //1a. mengganti path
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
                //1b. mengganti semua path child
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

    /** 
     * observe cluster event deleting
     * 1. updated parent + child path
     * 2. act, accept or refuse
     */
    public function deleting($model)
    {
		$errors 						= new MessageBag();

        //1. Check varian relationship with transaction
        if($model->products()->varians()->transactions()->count())
        {
            $errors->add('varian', 'Tidak dapat menghapus kategori yang berhubungan dengan produk varian yang pernah di stok &/ order.');
        }

        if($errors->count())
        {
			$model['errors'] 		= $errors;

        	return false;
        }

        return true;
    }
}
