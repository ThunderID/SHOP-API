<?php namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\ScopeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ReferencedByScope implements ScopeInterface  
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
		$builder->selectraw('IFNULL(users2.name, "BALIN") as reference_name')
				->leftjoin('point_logs', function($join)
				{
					$join->on('point_logs.user_id', '=', 'users.id')
					->where('point_logs.reference_type', '=', 'App\Models\User')
					->wherenull('point_logs.deleted_at')
					;
				})
				->leftjoin(DB::raw('(SELECT name, id, deleted_at from users) as users2'), function ($join)
				{
					$join->on('users2.id', '=', 'point_logs.reference_id')
					->wherenull('users2.deleted_at')
					;
				})
				;
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
	    // unset($query->wheres['type']);
	}
}
