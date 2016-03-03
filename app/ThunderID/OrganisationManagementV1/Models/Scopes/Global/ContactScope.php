<?php 

namespace App\OrganisationManagementV1\Models\Scopes\Global;

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
    	$builder->selectraw('IFNULL(if(item="phone", value, ""), "not available") as phone')
    			->selectraw('IFNULL(if(item="address", value, ""), "not available") as address')
    			->selectraw('IFNULL(if(item="email", value, ""), "not available") as email')
    			->leftjoin('contacts', function ($join) 
				 {
	                                    $join->on ( $model->getTable().'.id', '=', 'contacts.contactable_id' )
	                                    ->whereIn('contacts.contactable_type', $model->getName())
	                                    ->where('contacts.is_default', '=', true)
	                                    ->wherenull('contacts.deleted_at')
	                                    ;
				})
	    		->groupby($model->getTable().'.id')
	    		;
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
