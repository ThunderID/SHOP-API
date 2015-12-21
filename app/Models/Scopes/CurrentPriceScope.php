<?php namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\ScopeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CurrentPriceScope implements ScopeInterface  
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
		$builder
		->selectraw('products.*')
		->selectraw('prices.price as price')
		->selectraw('prices.promo_price as promo_price')
		->leftjoin('prices', function ($join) 
		 {
            $join->on ( 'prices.product_id', '=', 'products.id' )
			->on(DB::raw('(prices.started_at = (select max(started_at) from prices as tl2 where tl2.product_id = prices.product_id and tl2.deleted_at is null and tl2.started_at <= "'.date('Y-m-d H:i:s').'"))'), DB::raw(''), DB::raw(''))
            ->where('prices.started_at', '<=', date('Y-m-d H:i:s'))
            ->wherenull('prices.deleted_at')
            ;
		});
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