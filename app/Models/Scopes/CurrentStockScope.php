<?php namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\ScopeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Scope to count current stock of product/varian
 *
 * @return current_stock
 * @author cmooy
 */

class CurrentStockScope implements ScopeInterface  
{
	/**
	 * Apply the scope to a given Eloquent query builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $builder
	 * @param  \Illuminate\Database\Eloquent\Model  $model
	 * @return void
	 */
	public function apply(Builder $builder, Model $model)
	{
		if($model->getTable()=='products')
		{
			$builder->selectraw('products.*')
					->selectglobalstock(true)
					->LeftJoinVarianFromProduct(true)
					->LeftJoinTransactionDetailFromVarian(true)
					->LeftTransactionStockOn(['wait', 'paid', 'packed', 'shipping', 'delivered'])
					->groupby('products.id')
					;
		}
		else
		{
			$builder->selectraw('varians.*')
					->selectglobalstock(true)
					->LeftJoinTransactionDetailFromVarian(true)
					->LeftTransactionStockOn(['wait', 'paid', 'packed', 'shipping', 'delivered'])
					->groupby('varians.id')
					;
		}
	}

	/**
	 * Remove the scope from the given Eloquent query builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $builder
	 * @param  \Illuminate\Database\Eloquent\Model  $model
	 * @return void
	 */
	public function remove(Builder $builder, Model $model)
	{
	    $query = $builder->getQuery();
	    // unset($query);
	}
}
