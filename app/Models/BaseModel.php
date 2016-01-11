<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

use Illuminate\Support\MessageBag;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Abstract class for eloquent models Models
 * 
 * @author cmooy
 */
abstract class BaseModel extends Eloquent 
{

	/**
	 * use soft delete trait
	 *
	 */
	use SoftDeletes;

	protected $errors;

	/* ---------------------------------------------------------------------------- ERRORS ----------------------------------------------------------------------------*/
	
	/**
	 * return errors
	 *
	 * @return MessageBag
	 **/
	function getError()
	{
		return $this->errors;
	}

	/* ---------------------------------------------------------------------------- RELATIONSHIP ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- QUERY BUILDER ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- FUNCTIONS ----------------------------------------------------------------------------*/
		
	/**
	 * construct function, iniate error
	 *
	 */
	function __construct() 
	{
		parent::__construct();

		$this->errors = new MessageBag;
	}

	/**
	 * boot function inherit eloquent
	 *
	 */
	public static function boot() 
	{
        parent::boot();
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/

	/**
	 * scope search based on id (pk)
	 *
	 * @param string or array of id
	 */	
	public function scopeID($query, $variable)
	{
		if(is_array($variable))
		{
			return 	$query->whereIn($query->getModel()->table.'.id', $variable);
		}

		if(is_null($variable))
		{
			return $query;
		}

		return 	$query->where($query->getModel()->table.'.id', $variable);
	}

	/**
	 * scope search based on not id (pk)
	 *
	 * @param string or array of id
	 */	
	public function scopeNotID($query, $variable)
	{
		if(is_array($variable))
		{
			return 	$query->whereNotIn($query->getModel()->table.'.id', $variable);
		}

		if(is_null($variable))
		{
			return $query;
		}

		return 	$query->where($query->getModel()->table.'.id', '<>', $variable);
	}
}