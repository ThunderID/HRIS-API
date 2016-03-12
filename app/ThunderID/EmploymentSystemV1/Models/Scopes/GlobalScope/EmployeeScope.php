<?php 

namespace App\ThunderID\EmploymentSystemV1\Models\Scopes\GlobalScope;

use Illuminate\Database\Eloquent\ScopeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

		$prefix_person			= env('DB_PREFIX_HR_PERSON');
		$prefix_empl			= env('DB_PREFIX_HR_EMPLOYMENT');
		$prefix_org				= env('DB_PREFIX_HR_ORGANISATION');

    	$builder->join(DB::raw($prefix_empl.'works'), function ($join) use($end, $prefix_empl, $prefix_person)
				 {
	                                    $join->on ( DB::raw($prefix_person.'persons.id'), '=', DB::raw($prefix_empl.'works.person_id') )
	                                    ->where(function ($query)use($end, $prefix_empl)
									    	{
											    $query->wherenull( DB::raw($prefix_empl.'works.end'))
											    ->orwhere( DB::raw($prefix_empl.'works.end'), '>=', date('Y-m-d H:i:s', strtotime($end)));
											})
	                                    ;
				})
				->join(DB::raw($prefix_org.'charts'), function ($join) use ($prefix_empl, $prefix_org)
				 {
	                                    $join->on ( DB::raw($prefix_empl.'works.chart_id'), '=', DB::raw($prefix_org.'charts.id'))
	                                    ->wherenull(DB::raw($prefix_org.'charts.deleted_at'))
	                                    ;
				})
				->join(DB::raw($prefix_org.'branches'), function ($join) use ($prefix_org)
				 {
	                                    $join->on ( DB::raw($prefix_org.'charts.branch_id'), '=', DB::raw($prefix_org.'branches.id') )
	                                    ->wherenull(DB::raw($prefix_org.'branches.deleted_at'))
	                                    ;
				})
	    		->groupby(DB::raw($prefix_person.'persons.id'))
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
