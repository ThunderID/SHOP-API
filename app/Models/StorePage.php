<?php

/** 
	* Inheritance StoreSetting Model
	* For every inheritance model, allowed to have only $type, fillable, rules, and available function
*/

namespace App\Models;

// use App\Models\Observers\StorePageObserver;

class StorePage extends StoreSetting
{
	/**
	 * The public variable that assigned type of inheritance model
	 *
	 * @var string
	 */

	public $type					=	['about_us', 'why_join', 'term_and_condition'];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable				=	[
											'value'								,
											'started_at'						,
										];

	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'started_at'						=> 'date_format:"Y-m-d H:i:s"'/*|after: - 1 second'*/,
										];
	
	/* ---------------------------------------------------------------------------- RELATIONSHIP ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- QUERY BUILDER ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- FUNCTIONS ----------------------------------------------------------------------------*/
		
	public static function boot() 
	{
        parent::boot();
 
        // StorePage::observe(new StorePageObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
