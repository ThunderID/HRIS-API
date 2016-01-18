<?php namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\ScopeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Scope to get select raw of all variable
 *
 * @return record
 * @author cmooy
 */
class QuotaWorkleaveScope implements ScopeInterface  
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
		$builder->selectraw('IFNULL(SUM(quota),0) as workleave_quota')
		->leftjoin('person_workleaves', function ($join) 
				 {
	                                    $join->on ( 'persons.id', '=', 'person_workleaves.person_id' )
	                                    ->where('person_workleaves.start', '>=', 'NOW()')
	                                    ->where('person_workleaves.end', '<', 'NOW()')
	                                    ->wherenull('person_workleaves.deleted_at')
	                                    ;
				})
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
	    // unset($query);
	}
}
