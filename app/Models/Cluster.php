<?php

namespace App\Models;

// use App\Models\Observers\ClusterObserver;

use App\Models\Traits\HasTypeTrait;
use App\Models\Traits\HasStockTrait;
use App\Models\Traits\HasTransactionStatusTrait;

class Cluster extends BaseModel
{
	/* ---------------------------------------------------------------------------- RELATIONSHIP TRAITS ---------------------------------------------------------------------*/
	use \App\Models\Traits\belongsTo\HasClusterTrait;
	use \App\Models\Traits\belongsToMany\HasProductsTrait;
	
	/* ---------------------------------------------------------------------------- GLOBAL PLUG SCOPE TRAITS ---------------------------------------------------------------------*/
	use HasTypeTrait;
	use HasStockTrait;
	use HasTransactionStatusTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'categories';

	// protected $timestamps			= true;

	/**
	 * Timestamp field
	 *
	 * @var array
	 */
	protected $dates				=	['created_at', 'updated_at', 'deleted_at', 'transact_at'];

	/**
	 * The appends attributes from mutator and accessor
	 *
	 * @var array
	 */
	protected $appends				=	[];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden 				= [];

	/* ---------------------------------------------------------------------------- RELATIONSHIP ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- QUERY BUILDER ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- FUNCTIONS ----------------------------------------------------------------------------*/
	
	public static function boot() 
	{
        parent::boot();
 
        // Cluster::observe(new ClusterObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
