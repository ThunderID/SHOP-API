<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;

/**
 * Handle Protected Warehouse's report
 * 
 * @author cmooy
 */
class WarehouseController extends Controller
{
	/**
	 * Display product stock's movement
	 *
	 * @param skip, take
	 * @return Response
	 */
	public function card($id = null)
	{
		$varian                     = \App\Models\Varian::id($id);

		$detail                     = \App\Models\TransactionDetail::varianid($id)->stockmovement(true);

		if(Input::has('search'))
		{
			$search                 = Input::get('search');

			foreach ($search as $key => $value) 
			{
				switch (strtolower($key)) 
				{
					case 'ondate':
						$detail     = $detail->TransactionLogChangedAt($value);
						$varian     = $varian->TransactionLogChangedAt($value);
						break;
					default:
						# code...
						break;
				}
			}
		}
   
   		if(!$varian->first())
   		{
        	return new JSend('error', (array)Input::all(), 'ID Tidak valid.');
   		}

		$varian                     = $varian->with(['product'])->first()->toArray();

		$varian['details']          = $detail->get()->toArray();

		return new JSend('success', (array)$varian);
	}

	/**
	 * Display products critical stock
	 *
	 * @param skip, take
	 * @return Response
	 */
	public function critical()
	{
		$setting                    = \App\Models\Policy::ondate('now')->type('critical_stock')->first();

		if(!$setting)
		{
			$critical               = 0;
		}
		else
		{
			$critical               = $setting['value'];
		}

		$result                     = \App\Models\Varian::critical($critical);
		
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

		$result                     = $result->with(['product'])->get()->toArray();

		return new JSend('success', (array)['count' => $count, 'data' => $result]);
	}

	/**
	 * Display products critical stock
	 *
	 * @param skip, take
	 * @return Response
	 */
	public function opname()
	{
		$result                     = \App\Models\Varian::with(['product']);

		if(Input::has('search'))
		{
			$search                 = Input::get('search');

			foreach ($search as $key => $value) 
			{
				switch (strtolower($key)) 
				{
					case 'ondate':
						$result     = $result->TransactionLogChangedAt($value);
						break;
					default:
						# code...
						break;
				}
			}
		}
   
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
		$count                      = count($result->get(['id']));

		$result                     = $result->get()->toArray();

		return new JSend('success', (array)['count' => $count, 'data' => $result]);
	}
}
