<?php namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\ScopeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Scope to get default contact of branch/person
 *
 * @return phone, address, email
 * @author cmooy
 */
class ContactScope implements ScopeInterface  
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
		if(in_array(get_class($model), ['App\Models\Person', 'App\Models\Employee']))
		{
	    	$builder->selectraw('IFNULL(if(item="phone", value, ""), "not available") as phone')
	    			->selectraw('IFNULL(if(item="address", value, ""), "not available") as address')
	    			->selectraw('IFNULL(if(item="email", value, ""), "not available") as email')
	    			->leftjoin('contacts', function ($join) 
					 {
		                                    $join->on ( 'persons.id', '=', 'contacts.person_id' )
		                                    ->whereIn('contacts.person_type', ['App\Models\Person', 'App\Models\Employee'])
		                                    ->where('contacts.is_default', '=', true)
		                                    ->wherenull('contacts.deleted_at')
		                                    ;
					})
		    		->groupby('persons.id')
		    		;
		}
		else
		{
	    	$builder->selectraw('IFNULL(if(item="phone", value, ""), "not available") as phone')
	    			->selectraw('IFNULL(if(item="address", value, ""), "not available") as address')
	    			->selectraw('IFNULL(if(item="email", value, ""), "not available") as email')
	    			->leftjoin('contacts', function ($join) 
					 {
		                                    $join->on ( 'branches.id', '=', 'contacts.branch_id' )
		                                    ->whereIn('contacts.branch_type', ['App\Models\Branch', 'App\Models\Employee'])
		                                    ->where('contacts.is_default', '=', true)
		                                    ->wherenull('contacts.deleted_at')
		                                    ;
					})
		    		->groupby('branches.id')
		    		;
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
	    // unset($query->wheres['Employee']);
	}
}
