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
class GradeScope implements ScopeInterface  
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
		$prefix_empl			= 'hres_';//env('DB_PREFIX_HR_EMPLOYMENT');

		$builder->selectraw($prefix_empl.'grade_logs.grade as grade')
			->leftjoin(DB::raw($prefix_empl.'grade_logs'), function ($join) use($prefix_empl)
			 {
				$join->on ( DB::raw($prefix_empl.'works.id'), '=', DB::raw($prefix_empl.'grade_logs.work_id') )
					->on(DB::raw('('.$prefix_empl.'grade_logs.id = (select id from '.$prefix_empl.'grade_logs as tl2 where tl2.work_id = '.$prefix_empl.'grade_logs.work_id and tl2.deleted_at is null order by tl2.updated_at desc limit 1))'), DB::raw(''), DB::raw(''))
					->wherenull( DB::raw($prefix_empl.'grade_logs.deleted_at'))
				;
			})
			->groupby(DB::raw($prefix_empl.'works.id'));
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
		// unset($query->wheres['Grade']);
	}
}
