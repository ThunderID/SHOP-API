<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;

/**
 * Handle Protected reports
 * 
 * @author cmooy
 */
class ReportController extends Controller
{
	/**
	 * Display usage of voucher in transaction
	 *
	 * @param skip, take
	 * @return Response
	 */
	public function voucher()
	{
		$result                     = \App\Models\Sale::status(['paid', 'packed', 'shipping', 'delivered']);

		if(Input::has('search'))
		{
			$search                 = Input::get('search');

			foreach ($search as $key => $value) 
			{
				switch (strtolower($key)) 
				{
					case 'ondate':
						$result 	= $result->TransactionLogChangedAt($value);
                        break;
					default:
						# code...
						break;
				}
			}
		}

		$count                      = count($result->get(['id']));

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

		$result                     = $result->with(['user', 'transactiondetails', 'transactiondetails.varian', 'transactiondetails.varian.product', 'payment', 'paidpointlogs', 'paidpointlogs.referencepointvoucher', 'paidpointlogs.referencepointvoucher.referencevoucher', 'paidpointlogs.referencepointreferral', 'paidpointlogs.referencepointreferral.referencereferral', 'paidpointlogs.pointlog'])->get()->toArray();

		return new JSend('success', (array)['count' => $count, 'data' => $result]);
	}
	
	/**
	 * Display selled product
	 *
	 * @param skip, take
	 * @return Response
	 */
	public function product()
	{
		$result                     = new \App\Models\Varian;

		if(Input::has('search'))
		{
			$search                 = Input::get('search');

			foreach ($search as $key => $value) 
			{
				switch (strtolower($key)) 
				{
					case 'ondate':
						$result 	= $result->TransactionLogChangedAt($value);
                        break;
					default:
						# code...
						break;
				}
			}
		}

		$count                      = count($result->havingsolditem(0)->get(['id']));

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

		$result                     = $result->havingsolditem(0)->with(['product'])->get()->toArray();

		return new JSend('success', (array)['count' => $count, 'data' => $result]);
	}
}
