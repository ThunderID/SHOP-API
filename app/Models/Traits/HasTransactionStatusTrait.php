<?php namespace App\Models\Traits;

use DB;

trait HasTransactionStatusTrait 
{

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasTransactionStatusTraitConstructor()
	{
		//
	}

	public function scopeJoinTransactionFromShipment($query, $variable)
	{
		return $query
		 ->join('transactions', function ($join) use($variable) 
			 {
                                    $join->on ( 'shipments.transaction_id', '=', 'transactions.id' )
                                    ->wherenull('transactions.deleted_at')
                                    ;
			})
		;
	}

	public function scopeJoinTransaction($query, $variable)
	{
		return $query
		 ->join('transactions', function ($join) use($variable) 
			 {
                                    $join->on ( 'transaction_details.transaction_id', '=', 'transactions.id' )
                                    ->wherenull('transactions.deleted_at')
                                    ;
			})
		;
	}

	public function scopeLeftJoinTransaction($query, $variable)
	{
		return $query
		 ->leftjoin('transactions', function ($join) use($variable) 
			 {
                                    $join->on ( 'transaction_details.transaction_id', '=', 'transactions.id' )
                                    ->wherenull('transactions.deleted_at')
                                    
                                    ;
			})
		;
	}

	public function scopeTransactionLogStatus($query, $variable)
	{

		if(!is_array($variable))
		{
			return $query
			 ->join('transaction_logs', function ($join) use($variable) 
			 {
                                    $join->on ( 'transaction_logs.transaction_id', '=', 'transactions.id' )
									->on(DB::raw('(transaction_logs.id = (select id from transaction_logs as tl2 where tl2.transaction_id = transaction_logs.transaction_id and tl2.deleted_at is null order by tl2.changed_at desc limit 1))'), DB::raw(''), DB::raw(''))
                                    ->where('transaction_logs.status', '=', $variable)
                                    ->wherenull('transaction_logs.deleted_at')
                                    ;
			})
			;
		}
		else
		{
			return $query
			 ->join('transaction_logs', function ($join) use($variable) 
			 {
                                    $join->on ( 'transaction_logs.transaction_id', '=', 'transactions.id' )
									->on(DB::raw('(transaction_logs.id = (select id from transaction_logs as tl2 where tl2.transaction_id = transaction_logs.transaction_id and tl2.deleted_at is null order by tl2.changed_at desc limit 1))'), DB::raw(''), DB::raw(''))
                                    ->whereIn('transaction_logs.status', $variable)
                                    ->wherenull('transaction_logs.deleted_at')
                                    ;
			})
			;
		}
	}
	
	public function scopeLeftTransactionLogStatus($query, $variable)
	{

		if(!is_array($variable))
		{
			return $query
			 ->leftjoin('transaction_logs', function ($join) use($variable) 
			 {
                                    $join->on ( 'transaction_logs.transaction_id', '=', 'transactions.id' )
									->on(DB::raw('(transaction_logs.changed_at = (select id from transaction_logs as tl2 where tl2.transaction_id = transaction_logs.transaction_id and tl2.deleted_at is null order by tl2.changed_at desc limit 1))'), DB::raw(''), DB::raw(''))
                                    ->where('transaction_logs.status', '=', $variable)
                                    ->wherenull('transaction_logs.deleted_at')
                                    ;
			})
			;
		}
		else
		{
			return $query
			 ->leftjoin('transaction_logs', function ($join) use($variable) 
			 {
                                    $join->on ( 'transaction_logs.transaction_id', '=', 'transactions.id' )
									->on(DB::raw('(transaction_logs.changed_at = (select id from transaction_logs as tl2 where tl2.transaction_id = transaction_logs.transaction_id and tl2.deleted_at is null order by tl2.changed_at desc limit 1))'), DB::raw(''), DB::raw(''))
                                    ->whereIn('transaction_logs.status', $variable)
                                    ->wherenull('transaction_logs.deleted_at')
                                    ;
			})
			;
		}
	}
	
	public function scopeTransactionLogChangedAt($query, $variable)
	{
		if(!is_array($variable))
		{
			return $query->where('changed_at', '<=', date('Y-m-d H:i:s', strtotime($variable)))->orderBy('changed_at', 'desc');
		}

		return $query->where('changed_at', '>=', date('Y-m-d H:i:s', strtotime($variable[0])))->where('changed_at', '<=', date('Y-m-d H:i:s', strtotime($variable[1])))->orderBy('changed_at', 'asc');
	}
	

	public function scopeTransactionTransactAt($query, $variable)
	{
		if(!is_array($variable))
		{
			return $query->where('transact_at', '<=', date('Y-m-d H:i:s', strtotime($variable)))->orderBy('transact_at', 'desc');
		}

		return $query->where('transact_at', '>=', date('Y-m-d H:i:s', strtotime($variable[0])))->where('transact_at', '<=', date('Y-m-d H:i:s', strtotime($variable[1])))->orderBy('changed_at', 'asc');
	}
	public function scopeExtendTransactionType($query, $variable)
	{
		if(is_array($variable))
		{
			return $query->whereIn('transactions.type', $variable)
			;
		}
		else
		{
			return $query->where('transactions.type', $variable)
			;
		}
	}

	public function scopeTransactionStockOn($query, $variable)
	{
		return $query->jointransaction(true)->transactionlogstatus($variable)->extendtransactiontype(['sell', 'buy'])
		;
	}

	public function scopeLeftTransactionStockOn($query, $variable)
	{
		return $query->leftjointransaction(true)->lefttransactionlogstatus($variable)->extendtransactiontype(['sell', 'buy'])
		;
	}

	public function scopeTransactionSellOn($query, $variable)
	{
		return $query->jointransaction(true)->transactionlogstatus($variable)->extendtransactiontype('sell')
		;
	}

	public function scopeTransactionBuyOn($query, $variable)
	{
		return $query->jointransaction(true)->transactionlogstatus($variable)->extendtransactiontype('buy')
		;
	}

	public function scopeCategoryAncestorSuccessor($query, $variable)
	{
		if(!is_array($variable))
		{
			return $query
			 ->rightjoin('categories', function ($join) use($variable) 
			 {
                                    $join->on( 'categories.id', '=', 'categories_products.category_id' )
                                    // ->oron('categories.id', '=', 'categories_products.category_id')
                                    // ->where('categories.category_id', '=', $variable)
                                    ->where('categories.id', '=', $variable)
                                    ->wherenull('categories.deleted_at')
                                    ;
			})
			;
		}
		else
		{
			return $query
			 ->rightjoin('categories', function ($join) use($variable) 
			 {
                                    $join->on ( 'categories.id', '=', 'categories_products.category_id' )
                                    ->oron('categories.id', 'categories_products.category_id')
                                    ->where('categories.category_id', '=', $variable)
                                    ->orwhere('categories.id', '=', $variable)
                                    ->wherenull('categories.deleted_at')
                                    ;
			})
			;
		}
	}

	public function scopeLeftJoinVarianFromProduct($query, $variable)
	{
		return $query
		 ->leftjoin('varians', function ($join) use($variable) 
		 {
            $join->on ( 'varians.product_id', '=', 'products.id' )
            ->wherenull('varians.deleted_at')
            ;
		})
		;
	}

	public function scopeLeftJoinTransactionDetailFromVarian($query, $variable)
	{
		return $query
		 ->leftjoin('transaction_details', function ($join) use($variable) 
		 {
            $join->on ( 'transaction_details.varian_id', '=', 'varians.id' )
            ->wherenull('transaction_details.deleted_at')
            ;
		})
		;
	}
}