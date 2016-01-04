<?php namespace App\Models\Observers;

/* ----------------------------------------------------------------------
 * Event:
 * ---------------------------------------------------------------------- */

class TransactionObserver 
{
	public function saving($model)
	{
		return false;
	}
}
