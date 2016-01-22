<?php namespace App\Models\Traits;

use Illuminate\Support\Facades\DB;

/**
 * available function to get result of stock
 *
 * @author cmooy
 */
trait HasEmployeeScheduleTrait 
{
	/**
	 * boot
	 *
	 * @return void
	 **/
	function HasEmployeeScheduleTraitConstructor()
	{
		//
	}

	/**
	 * inner joining person from anywhere
	 *
	 **/
	public function scopeJoinPerson($query, $variable)
	{
		return $query
			->join('persons', function ($join) use($variable) 
				 {
	                $join->on ( 'persons.id', '=', $this->getTable().'.person_id' )
	                ->wherenull('persons.deleted_at')
	                ;
				})
				;
	}

	/**
	 * inner joining work from person
	 *
	 **/
	public function scopeJoinWorkFromPersonOn($query, $variable)
	{
		return $query
			->join('works', function ($join) use ($variable)
				{
					$join->on ( 'persons.id', '=', 'works.person_id' )
					    ->where(function ($query) use($variable) 
					    	{
							    $query->wherenull('works.end')
							    ->orwhere('works.end', '>=', $variable->format('Y-m-d'));
							})
					    ->wherenull('works.deleted_at')
					    ;
				})
			;
	}

	/**
	 * outer joining person schedule from persons
	 *
	 **/
	public function scopeJoinPersonHasNoScheduleOn($query, $variable)
	{
		return $query
			->join('persons', function ($join) use($variable) 
				 {
	                 $join->on( 'logs.person_id', '=', 'persons.id' )
									->on(DB::raw('(persons.id <> (select person_id from person_schedules as ps where ps.person_id = logs.person_id and ps.deleted_at is null and ps.on = '.$variable->format('Y-m-d').' limit 1))'), DB::raw(''), DB::raw(''))
                                    ->wherenull('persons.deleted_at');
				})
			;
	}
}