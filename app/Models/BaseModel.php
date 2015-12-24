<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

use Illuminate\Support\MessageBag;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class BaseModel extends Eloquent 
{
	use SoftDeletes;

	protected $errors;

	/* ---------------------------------------------------------------------------- ERRORS ----------------------------------------------------------------------------*/
	/**
	 * return errors
	 *
	 * @return MessageBag
	 * @author 
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
	
	function __construct() 
	{
		parent::__construct();

		$this->errors = new MessageBag;
	}

	public static function boot() 
	{
        parent::boot();
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/
	
	public function scopeID($query, $variable)
	{
		if(is_array($variable))
		{
			return 	$query->whereIn($query->getModel()->table.'.id', $variable);
		}

		return 	$query->where($query->getModel()->table.'.id', $variable);
	}

	public function scopeNotID($query, $variable)
	{
		if(is_array($variable))
		{
			return 	$query->whereNotIn($query->getModel()->table.'.id', $variable);
		}

		return 	$query->where($query->getModel()->table.'.id', '<>', $variable);
	}
}