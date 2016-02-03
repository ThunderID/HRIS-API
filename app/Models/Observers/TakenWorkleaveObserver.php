<?php namespace App\Models\Observers;

use \Illuminate\Support\MessageBag as MessageBag;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;

/**
 * Used in Takenworkleave model
 *
 * @author cmooy
 */
class TakenWorkleaveObserver 
{
	/** 
     * observe Takenworkleave event saving
     * 1. count quotas
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function saving($model)
	{
		$errors					= new MessageBag;

		//1. count quotas
		$quota 					= 0;
		if($model->person()->count() && $model->work()->count())
		{
			$from					= Carbon::createFromFormat('Y-m-d', $model->start);
			$to						= Carbon::createFromFormat('Y-m-d', $modenl->end);
			$days 					= $from->diffInDays($to);

			$interval 				= new DateInterval('P1D');
			$to->add($interval);

			$daterange 				= new DatePeriod($from, $interval ,$to);

			foreach($daterange as $date)
			{
				//1a. check person schedule
				$ps					= \App\Models\PersonSchedule::ondate($date->format('Y-m-d'))->personid($model->person_id)->first();

				if(!$ps)
				{
					//1b. check schedule
					$s				= \App\Models\Schedule::ondate($date->format('Y-m-d'))->calendarid($model->work->calendar_id)->first();
					if(!$s)
					{
						//1c. check calendar
						$c			= \App\Models\Calendar::id($model->work->calendar_id)->first();
						if($c)
						{
							$harikerja 	= explode(',', $c['workdays']);
							$day		= 	[
												'senin' => 'monday', 'selasa' => 'tuesday', 'rabu' => 'wednesday', 'kamis' => 'thursday', 'jumat' => 'friday', 'sabtu' => 'saturday', 'minggu' => 'sunday',
												'monday' => 'monday', 'tuesday' => 'tuesday', 'wednesday' => 'wednesday', 'thursday' => 'thursday', 'friday' => 'friday', 'saturday' => 'saturday', 'sunday' => 'sunday'
											];
							
							$workdays 	= [];

							//translate to ing
							foreach ($harikerja as $key => $value) 
							{
								$workdays[]								= $day[strtolower($value)];
							}

							if(!in_array(strtolower($date->format('l')), $workdays))
							{
								$quota 	= $quota + 1;
							}

						}
					}
					elseif(in_array($s['status'], ['HB', 'DN']))
					{
						$quota 			= $quota + 1;
					}
				}
				elseif(in_array($ps['status'], ['HB', 'DN']))
				{
					$quota 			= $quota + 1;
				}
			}
		}

		$model->quota 			= $quota;

        if($errors->count())
        {
			$model['errors']	= $errors;

        	return false;
        }

        return true;
	}


	/** 
     * observe Takenworkleave event saved
     * 1. check employee schedule on range workleave taken
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function saved($model)
	{
		$errors					= new MessageBag;

		//1. check employee schedule on range workleave taken
		if($model->person()->count())
		{
			$from					= Carbon::createFromFormat('Y-m-d', $model->start);
			$to						= Carbon::createFromFormat('Y-m-d', $modenl->end);
			$days 					= $from->diffInDays($to);

			$interval 				= new DateInterval('P1D');
			$to->add($interval);

			$daterange 				= new DatePeriod($from, $interval ,$to);

			foreach($daterange as $date)
			{
				$ps					= \App\Models\PersonSchedule::ondate($date->format('Y-m-d'))->personid($model->person_id)->first();

				if(!$ps)
				{
					$schedule 		= 	[
											'person_id'						=> $model->person_id,
											'name'							=> $value['name'],
											'status'						=> $value['status'],
											'on'							=> $model->on->format('Y-m-d'),
											'start'							=> '00:00:00',
											'end'							=> '00:00:00',
											'break_idle'					=> '0',
										];

					$pschedule 		= new \App\Models\schedule;

					$pschedule->fill($schedule);

					if(!$pschedule->save())
					{
						$errors->add('Schedule', $pschedule->getError());
					}
				}
			}
		}

        if($errors->count())
        {
			$model['errors']	= $errors;

        	return false;
        }

        return true;
	}

	/** 
     * observe Takenworkleave event deleted
     * 1. check employee on that personschedule
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function deleted($model)
	{
		$errors					= new MessageBag;

		//1. check employee on that day logs
		if($model->person()->count())
		{
			$ps				= \App\Models\PersonSchedule::ondate($model->on->format('Y-m-d'))->personid($model->person_id)->get();

			if($ps)
			{
				foreach ($ps as $key => $value) 
				{
					if(!$value->delete())
					{
						$errors->add('Schedule', $value->getError());
					}
				}
			}
		}

        if($errors->count())
        {
			$model['errors']	= $errors;

        	return false;
        }

        return true;
	}
}
