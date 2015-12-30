<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

use App\Models\Varian;

/* ----------------------------------------------------------------------
 * Event:
 * saving
 * deleting
 * ---------------------------------------------------------------------- */

class VarianObserver 
{
	public function saving($model)
    {
		$errors 						= new MessageBag();

		//1. Check unique SKU
        $sku                            = Varian::sku($model->sku)->notid($model->id)->first();

        if(!is_null($sku))
        {
			$errors->add('sku', 'SKU sudah terdaftar.');
        }
        
        if($errors->count())
        {
			$model['errors'] 		= $errors;

        	return false;
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
