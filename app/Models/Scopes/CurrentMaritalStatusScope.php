<?php namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\ScopeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Scope to get current marital status
 *
 * @return marital_status
 * @author cmooy
 */
class CurrentMaritalStatusScope implements ScopeInterface  
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
		$builder->selectraw('IFNULL(marital_statuses.status,"tidak ada") as marital_status')
		->leftjoin('marital_statuses', function ($join) 
				 {
	                                    $join->on ( 'persons.id', '=', 'marital_statuses.person_id' )
										->on(DB::raw('(marital_statuses.id = (select id from marital_statuses as ms2 where ms2.person_id = marital_statuses.person_id and ms2.deleted_at is null order by ms2.id desc limit 1))'), DB::raw(''), DB::raw(''))
	                                    ->wherenull('marital_statuses.deleted_at')
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
