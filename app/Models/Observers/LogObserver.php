<?php namespace App\Models\Observers;

use App\Models\Policy;
use App\Models\Queue;
use App\Models\Log;
use App\Models\ProcessLog;
use App\Models\Employee;
use Carbon\Carbon;

/**
 * Used in Log model
 *
 * @author cmooy
 */
class LogObserver 
{
	/** 
     * observe Employee event saved
     * 1. check if prev day
     * 2. save into queue
     * 3. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function saved($model)
	{
		//1. check if prev day
		$date_on 						= $model->on->startOfDay();
		$date_today 					= Carbon::now()->startOfDay();

		if($date_today->diffInDays($date_on) >= 1 && $model->person()->count())
		{
			//2. save into queue
			$on 						= $date_on->format('Y-m-d');

			$idle_rule 					= new Policy;
			$idle_rule_1 				= $idle_rule->organisationid($model->person->organisation_id)->type('firstidle')->OnDate($on)->orderBy('started_at', 'desc')->first();
			$idle_rule_2 				= $idle_rule->organisationid($model->person->organisation_id)->type('secondidle')->OnDate($on)->orderBy('started_at', 'desc')->first();
			$idle_rule_3 				= $idle_rule->organisationid($model->person->organisation_id)->type('thirdidle')->OnDate($on)->orderBy('started_at', 'desc')->first();
			
			$margin_bottom_idle 		= 900;
			$idle_1 					= 3600;
			$idle_2 					= 7200;
			
			if($idle_rule_1)
			{
				$margin_bottom_idle 		= (int)$idle_rule_1->value;
			}
			if($idle_rule_2)
			{
				$idle_1 					= (int)$idle_rule_2->value;
			}
			if($idle_rule_3)
			{
				$idle_2 					= (int)$idle_rule_3->value;
			}

			$employee 						= new Employee;
			$employee->workend				= $date_on->format('Y-m-d H:i:s');
			$employee 						= count($employee->organisationid($model->person->organisation_id)->get(['id']));

			$parameter['margin_bottom_idle']= $margin_bottom_idle;
			$parameter['idle_1']			= $idle_1;
			$parameter['idle_2']			= $idle_2;
			$parameter['on']				= $on;

			$parameter['organisation_id']	= $model->person->organisation_id;

			$check 							= Queue::where('parameter', json_encode($parameter))->where('process_name', 'hr:logobserver')->where('process_number', '0')->first();
			
			if(!$check)
			{
				$queue 							= new Queue;
				$queue->fill([
						'process_name' 			=> 'hr:logobserver',
						'parameter' 			=> json_encode($parameter),
						'total_process' 		=> $employee,
						'task_per_process' 		=> 1,
						'process_number' 		=> 0,
						'total_task' 			=> $employee,
						'message' 				=> 'Initial Commit',
				]);

				if(!$queue->save())
				{
					$model['errors']		= $queue->getError();

					return false;
				}
				else
				{
					return true;
				}
			}
		}

		return true;
	}

	/** 
     * observe Employee event deleting
     * 1. check if prev day
     * 2. delete logs and process logs
     * 3. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function deleting($model)
	{
		//1. check if prev day
		if($model->on->format('Y-m-d') <= Carbon::now()->format('Y-m-d'))
		{
			$model['errors'] 					= ['Tidak dapat menghapus log yang sudah lewat dari tanggal hari ini.'];

			return false;
		}

		//2. delete logs and process logs
		$logs 									= Log::personid($model->person_id)->ondate([$model->on->startOfDay()->format('Y-m-d H:i:s'), $model->on->endOfDay()->format('Y-m-d H:i:s')])->get();

		if($logs->count() && $logs->count() <= 1)
		{
			$processes 							= ProcessLog::personid($model->person_id)->ondate([$model->on->startOfDay()->format('Y-m-d H:i:s'), $model->on->endOfDay()->format('Y-m-d H:i:s')])->get();

			foreach ($processes as $key => $value) 
			{
				if(!$value->delete())
				{
					$model['errors'] 			= $value->getError();
					
					return false;
				}
			}
		}
	}
}
