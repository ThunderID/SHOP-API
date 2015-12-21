<?php namespace App\Models\Observers;

/* ----------------------------------------------------------------------
 * Event:
 * ---------------------------------------------------------------------- */

class TransactionObserver 
{
	public function saving($model)
	{
		dd('salah');
		return false;
	}
}
