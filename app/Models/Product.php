<?php

namespace App\Models;

use App\Models\Traits\HasCurrentPriceTrait;
use App\Models\Traits\HasCurrentStockTrait;
use App\Models\Traits\HasDefaultImageTrait;

use App\Models\Traits\HasStockTrait;
use App\Models\Traits\HasTransactionStatusTrait;
// use App\Models\Observers\ProductObserver;

class Product extends BaseModel
{
	/* ---------------------------------------------------------------------------- RELATIONSHIP TRAITS ---------------------------------------------------------------------*/
	use \App\Models\Traits\hasMany\HasVariansTrait;

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
	
	// public function getPriceAttribute($value)
	// {
	// 	if(isset($this->price))
	// 	{
	// 		return $this->price;
	// 	}
	// 	else
	// 	{
	// 		$price 						= Price::productid($this->id)->ondate('now')->first();
	// 		if($price)
	// 		{
	// 			return $price->price;
	// 		}
	// 		else
	// 		{
	// 			return 0;
	// 		}
	// 	}

	// 	return 0;
	// }

	// public function getPromoPriceAttribute($value)
	// {
	// 	if(isset($this->price))
	// 	{
	// 		$price 						= $this->current_promo_price;
	// 	}
	// 	else
	// 	{
	// 		$promo 						= Price::productid($this->id)->ondate('now')->first();
	// 		if($promo)
	// 		{
	// 			$price 					= $promo->promo_price;
	// 		}
	// 		else
	// 		{
	// 			$price 					= 0;
	// 		}
	// 	}

	// 	return $price;
	// }

	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- FUNCTIONS ----------------------------------------------------------------------------*/
		
	public static function boot() 
	{
        parent::boot();
 
        // Product::observe(new ProductObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
