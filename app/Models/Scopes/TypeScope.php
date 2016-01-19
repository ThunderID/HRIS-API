<?php namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\ScopeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Scope to get precise type of inheritance model
 *
 * @param type
 * @author cmooy
 */
class TypeScope implements ScopeInterface  
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
		if(isset($model->type_field) && isset($model->type))
		{
			if(!is_array($model->type))
			{
		    	$builder->where($model->type_field, $model->type);
			}
			else
			{
		    	$builder->whereIn($model->type_field, $model->type);
			}
		}
		elseif(isset($model->type))
		{
			if(!is_array($model->type))
			{
		    	$builder->where('type', $model->type);
			}
			else
			{
		    	$builder->whereIn('type', $model->type);
			}
		}
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
	    unset($query->wheres['type']);
	}
}
