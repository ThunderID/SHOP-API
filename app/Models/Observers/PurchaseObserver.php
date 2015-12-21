<?php namespace App\Models\Observers;

/* ----------------------------------------------------------------------
 * Event:
 * ---------------------------------------------------------------------- */

class PurchaseObserver 
{
	public function saving($model)
	{
		return true;
	}
}
