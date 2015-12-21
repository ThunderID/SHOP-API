<?php namespace App\Models\Traits;

trait HasStockTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasStockTraitConstructor()
	{
		//
	}

	public function scopeSelectStockInCart($query, $variable)
	{
		return $query->selectraw('IFNULL(SUM(
									if(transactions.type ="sell", if(transaction_logs.status ="cart", quantity, 0), 0)
									),0) as cart_item')
		;
	}
	
	public function scopeSelectCurrentStock($query, $variable)
	{
		return $query->selectraw('IFNULL(SUM(
									if(transactions.type ="sell", if(transaction_logs.status ="wait" OR transaction_logs.status ="paid" OR transaction_logs.status ="shipping" OR transaction_logs.status="packed" OR transaction_logs.status ="delivered", 0-quantity, 0), quantity)
									),0) as current_stock')
		;
	}
	
	public function scopeSelectOnHoldStock($query, $variable)
	{
		return $query->selectraw('IFNULL(SUM(
									if(transactions.type ="sell", if(transaction_logs.status ="wait", quantity, 0), 0)
									),0) as on_hold_stock')
		;
	}
	public function scopeSelectPackedStock($query, $variable)
	{
		return $query->selectraw('IFNULL(SUM(
									if(transactions.type ="sell", if(transaction_logs.status ="packed", quantity, 0), 0)
									),0) as packed_stock')
		;
	}

	public function scopeSelectInventoryStock($query, $variable)
	{
		return $query->selectraw('IFNULL(SUM(
									if(transactions.type ="sell", if(transaction_logs.status ="shipping" OR transaction_logs.status="packed" OR transaction_logs.status ="delivered", 0-quantity, 0), quantity)
									),0) as inventory_stock')
		;
	}

	public function scopeSelectReservedStock($query, $variable)
	{
		return $query->selectraw('IFNULL(SUM(
									if(transactions.type ="sell", if(transaction_logs.status ="paid", quantity, 0), 0)
									),0) as reserved_stock')
		;
	}

	public function scopeSelectSoldItem($query, $variable)
	{
		return $query->selectraw('IFNULL(SUM(quantity),0) sold_item')
		;
	}

	public function scopeSelectGlobalStock($query, $variable)
	{
		return $query->selectcurrentstock(true)->selectonholdstock(true)->selectinventorystock(true)->selectreservedstock(true)->selectpackedstock(true);
		;
	}

	public function scopeJoinTransactionDetailFromProduct($query, $variable)
	{
		return $query
		->join('varians', function ($join) use($variable) 
			 {
                                    $join->on ( 'varians.product_id', '=', 'products.id' )
                                    ->wherenull('varians.deleted_at')
                                    ;
			})
		->join('transaction_details', function ($join) use($variable) 
			 {
                                    $join->on ( 'transaction_details.varian_id', '=', 'varians.id' )
                                    ->wherenull('transaction_details.deleted_at')
                                    ;
			})
		;
	}

	public function scopeJoinTransactionDetailFromVarian($query, $variable)
	{
		return $query
		->join('transaction_details', function ($join) use($variable) 
			 {
                                    $join->on ( 'transaction_details.varian_id', '=', 'varians.id' )
                                    ->wherenull('transaction_details.deleted_at')
                                    ;
			})
		;
	}

	public function scopeJoinVarianFromTransactionDetail($query, $variable)
	{
		return $query
		->join('varians', function ($join) use($variable) 
			 {
                                    $join->on ( 'varians.id', '=', 'transaction_details.varian_id' )
                                    ->wherenull('varians.deleted_at')
                                    ;
			})
		;
	}

	public function scopeHavingCurrentStock($query, $variable)
	{
		if($variable < 0)
		{
			$param 					= '<=';
			$variable 				= abs($variable);
		}
		else
		{
			$param 					= '>';
			$variable 				= abs($variable);
		}

		return $query->havingraw('IFNULL(SUM(
									if(transactions.type ="sell", if(transaction_logs.status ="wait" OR transaction_logs.status ="paid" OR transaction_logs.status ="shipping" OR transaction_logs.status="packed" OR transaction_logs.status ="delivered", 0-quantity, 0), quantity)
									),0) '.$param.' '.$variable)
		;
	}
}