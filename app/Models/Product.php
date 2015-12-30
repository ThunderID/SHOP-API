<?php

namespace App\Models;

use App\Models\Traits\HasCurrentPriceTrait;
use App\Models\Traits\HasCurrentStockTrait;
use App\Models\Traits\HasDefaultImageTrait;

use App\Models\Traits\HasStockTrait;
use App\Models\Traits\HasTransactionStatusTrait;
use App\Models\Observers\ProductObserver;

class Product extends BaseModel
{
	/* ---------------------------------------------------------------------------- RELATIONSHIP TRAITS ---------------------------------------------------------------------*/
	use \App\Models\Traits\hasMany\HasVariansTrait;
	use \App\Models\Traits\hasMany\HasPricesTrait;
	use \App\Models\Traits\hasMany\HasLabelsTrait;

	use \App\Models\Traits\belongsToMany\HasClustersTrait;

	use \App\Models\Traits\morphMany\HasImagesTrait;

	/* ---------------------------------------------------------------------------- GLOBAL SCOPE TRAITS ---------------------------------------------------------------------*/
	use HasCurrentStockTrait;
	use HasCurrentPriceTrait;
	use HasDefaultImageTrait;

	/* ---------------------------------------------------------------------------- GLOBAL PLUG SCOPE TRAITS ---------------------------------------------------------------------*/
	use HasStockTrait;
	use HasTransactionStatusTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'products';

	// protected $timestamps			= true;

	/**
	 * Timestamp field
	 *
	 * @var array
	 */
	protected $dates				=	['created_at', 'updated_at', 'deleted_at'];

	/**
	 * The appends attributes from mutator and accessor
	 *
	 * @var array
	 */
	protected $appends				=	[
											// 'price',
											// 'promo_price',
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden 				= [];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */

	protected $fillable				=	[
											'name'							,
											'upc'							,
											'slug'							,
											'description'					,
										];
										
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'name'							=> 'required|max:50',
											'upc'							=> 'required|max:255',
											'slug'							=> 'required|max:255',
										];
	
	/* ---------------------------------------------------------------------------- RELATIONSHIP ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- QUERY BUILDER ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- FUNCTIONS ----------------------------------------------------------------------------*/
		
	public static function boot() 
	{
        parent::boot();
 
        Product::observe(new ProductObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
