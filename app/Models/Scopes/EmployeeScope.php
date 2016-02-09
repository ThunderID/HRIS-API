<?php namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\ScopeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Scope to get employment
 *
 * @return current_job
 * @author cmooy
 */
class EmployeeScope implements ScopeInterface  
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
		if(isset($model->workend))
		{
			$end 				= $model->workend;
		}
		else
		{
			$end 				= 'now';
		}

    	$builder->selectraw('CONCAT(charts.name, " cabang ", branches.name) as current_job')
    			->selectraw('works.calendar_id as current_calendar_id')
    			->selectraw('works.grade as current_grade')
    			->join('works', function ($join) use($end)
				 {
	                                    $join->on ( 'persons.id', '=', 'works.person_id' )
	                                    ->where(function ($query)use($end)
									    	{
											    $query->wherenull('works.end')
											    ->orwhere('works.end', '>=', date('Y-m-d H:i:s', strtotime($end)));
											})
	                                    ;
				})
				->join('charts', function ($join) 
				 {
	                                    $join->on ( 'works.chart_id', '=', 'charts.id' )
	                                    ->wherenull('charts.deleted_at')
	                                    ;
				})
				->join('branches', function ($join) 
				 {
	                                    $join->on ( 'charts.branch_id', '=', 'branches.id' )
	                                    ->wherenull('branches.deleted_at')
	                                    ;
				})
	    		->groupby('persons.id')
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
