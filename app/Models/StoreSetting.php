<?php

namespace App\Models;

use App\Models\Traits\HasTypeTrait;
use App\Models\Observers\StoreSettingObserver;

/**
 * Used for StoreSetting, Policy, Store, Page, Slider Models
 * 
 * @author cmooy
 */
class StoreSetting extends BaseModel
{
	/**
	 * Global traits used as query builder (global scope).
	 *
	 */
	use HasTypeTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table				= 'tmp_store_settings';

	/**
	 * Timestamp field
	 *
	 * @var array
	 */
	// protected $timestamps			= true;
	
	/**
	 * Date will be returned as carbon
	 *
	 * @var array
	 */
	protected $dates				=	['created_at', 'updated_at', 'deleted_at', 'started_at', 'ended_at'];

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
	
	/**
	 * boot
	 * observing model
	 *
	 */
	public static function boot() 
	{
        parent::boot();
 
        StoreSetting::observe(new StoreSettingObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
	
	/**
	 * scope to find type of store setting
	 *
	 * @param string of type
	 */
	public function scopeType($query, $variable)
	{
		if(is_array($variable))
		{
			return 	$query->whereIn('type', $variable);
		}

		return 	$query->where('type', $variable);
	}
	
	/**
	 * scope to find history of date
	 *
	 * @param string of history
	 */
	public  function scopeOndate($query, $variable)
	{
		if(!is_array($variable))
		{
			return $query->where('started_at', '<=', date('Y-m-d H:i:s', strtotime($variable)))->orderBy('started_at', 'desc');
		}

		return $query->where('started_at', '>=', date('Y-m-d H:i:s', strtotime($variable[0])))->where('started_at', '<=', date('Y-m-d H:i:s', strtotime($variable[1])))->orderBy('started_at', 'asc');
	}
}
