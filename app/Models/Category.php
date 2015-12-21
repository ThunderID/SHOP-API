<?php

/** 
	* Inheritance Cluster Model
	* For every inheritance model, allowed to have only $type, fillable, rules, and available function
*/

namespace App\Models;

// use App\Models\Observers\CategoryObserver;

class Category extends Cluster
{
	/**
	 * The public variable that assigned type of inheritance model
	 *
	 * @var string
	 */

	public $type					=	'category';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */

	protected $fillable				=	[
											'category_id'					,
											'type'							,
											'path'							,
											'name'							,
											'slug'							,
										];

	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'path'							=> 'required|max:255',
											'name'							=> 'required|max:255',
											'slug'							=> 'max:255',
										];
	
	/* ---------------------------------------------------------------------------- RELATIONSHIP ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- QUERY BUILDER ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- FUNCTIONS ----------------------------------------------------------------------------*/
		
	public static function boot() 
	{
        parent::boot();
 
        Category::observe(new CategoryObserver());
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
}
