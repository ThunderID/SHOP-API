<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Tool to help broadcasting process
 * 
 * @author cmooy
 */
class BroadcastController extends Controller
{
	/**
	 * Store a queue
	 *
	 * 1. Validate Price Parameter
	 * 
	 * @return Response
	 */
	public function price()
	{
		if(!Input::has('price'))
		{
			return new JSend('error', (array)Input::all(), 'Tidak ada data price.');
		}

		$errors						= new MessageBag();

		DB::beginTransaction();

		//1. Validate Price Parameter
		$price						= Input::get('price');

		$price_rules				=   [
											'discount_amount'		=> 'required_without:discount_percentage|numeric',
											'discount_percentage'	=> 'required_without:discount_amount|numeric',
											'started_at'			=> 'required|date_format:"Y-m-d H:i:s"',
											'ended_at'				=> 'required|date_format:"Y-m-d H:i:s"',
											'category_ids'			=> 'required_if:item,category|array',
											'tag_ids'				=> 'required_if:item,tag|array',
											'is_labeled'			=> 'boolean',
										];

		$validator                  = Validator::make($price, $price_rules);

		if (!$validator->passes())
		{
			$errors->add('Price', $validator->errors());
		}
		else
		{
			$products 				= new \App\Models\Product;
			$products 				= $products->sellable(true);

			if(isset($price['category_ids']))
			{
				$products 			= $products->categoriesid($price['category_ids']);
			}
			elseif(isset($price['tag_ids']))
			{
				$products 			= $products->tagsid($price['tag_ids']);
			}

			$products 				= $products->get(['id']);

			$parameter				= $price;

			$queue 					= new \App\Models\Queue;
			$queue->fill([
				'process_name'		=> 'broadcast:discount',
				'parameter'			=> json_encode($parameter),
				'total_process'		=> count($products),
				'task_per_process'	=> 1,
				'process_number'	=> 0,
				'total_task'		=> count($products),
				'message'			=> 'Initial Commit',
			]);

			if(!$queue->save())
			{
				$errors->add('Product', $queue->getError());
			}
		}
		//End of validate product

		if($errors->count())
		{
			DB::rollback();

			return new JSend('error', (array)Input::all(), $errors);
		}

		DB::commit();
		
		$final_queue				= \App\Models\Queue::id($queue['id'])->first()->toArray();

		return new JSend('success', (array)$final_queue);
	}
}
