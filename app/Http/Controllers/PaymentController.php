<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * Handle Protected Resource of Sale
 * 
 * @author cmooy
 */
class PaymentController extends Controller
{
	/**
	 * Veritrans Credit Card
	 *
	 * 1. Check Order
	 * 2. Save Payment
	 * 
	 * @return Response
	 */
	public function veritranscc()
	{
		if(!Input::has('order_id'))
		{
			return new JSend('error', (array)Input::all(), 'Tidak ada data order id.');
		}

		$errors                     = new MessageBag();

		DB::beginTransaction();

		//1. Validate Sale Parameter
		$order						= Input::only('order_id', 'gross_amount', 'payment_type', 'masked_card', 'transaction_id');

		//1a. Get original data
		$sale_data					= \App\Models\Sale::findorfail($order['order_id']);

		//2. Save Payment
		$paid_data					= new \App\Models\Payment;
	
		$payment['transaction_id']	= $sale_data['id'];
		$payment['method']			= $order['payment_type'];
		$payment['destination']		= 'Veritrans';
		$payment['account_name']	= $order['masked_card'];
		$payment['account_number']	= $order['transaction_id'];
		$payment['ondate']			= \Carbon\Carbon::parse($order['transaction_time'])->format('Y-m-d H:i:s');
		$payment['amount']			= $order['gross_amount'];

		$paid_data					= $paid_data->fill($payment);

		if(!$paid_data->save())
		{
			$errors->add('Log', $paid_data->getError());
		}

		if($errors->count())
		{
			DB::rollback();

			return response()->json(new JSend('error', (array)Input::all(), $errors), 404);
		}

		DB::commit();
		
		$final_sale					= \App\Models\Sale::id($sale_data['id'])->with(['voucher', 'transactionlogs', 'user', 'transactiondetails', 'transactiondetails.varian', 'transactiondetails.varian.product', 'paidpointlogs', 'payment', 'shipment', 'shipment.address', 'shipment.courier', 'transactionextensions', 'transactionextensions.productextension'])->first()->toArray();

		return response()->json(new JSend('success', (array)$final_sale), 200);
	}
}
