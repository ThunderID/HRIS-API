<?php namespace App\Models\Observers;

use \Illuminate\Support\MessageBag as MessageBag;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;

/**
 * Used in GivenWorkleave model
 *
 * @author cmooy
 */
class GivenWorkleaveObserver 
{
	/** 
     * observe GivenWorkleave event saving
     * Case progressive workleave
     * 1. check workleave quota
     * 2. check start work
     * 3. check start work date
     * 4. check if progressive workleave is end of year and quota over 12
     * Case special workleave
     * 1. count quota
     * 
     * @param $model
     * @return bool
     */
	public function saving($model)
	{
		$errors					= new MessageBag;

		//case a. if given workleave was progressive
		if($model->work()->count() && $model->status == 'CN')
		{
			//1. check workleave quota
			if($model->workleave()->count())
			{
				$wleave_quota 	= $follow->workleave->quota;
			}
			else
			{
				$wleave_quota 	= 12;
			}

			//2. check start work
			$prev_work		= Work::personid($model->person_id)->orderby('start', 'asc')->notid($model->work_id)->first();

			if($prev_work)
			{
				$start_work = $prev_work->start;
			}
			else
			{
				$start_work = $model->work->start;
			}

			//2a. if work over a year ::
			//- end was end of start year + 3 months
			//- start could be taken now
			if($start_work->diffInYears($model->start) >= 1)
			{
				$extendpolicy 	= \App\Models\Policy::organisationid($model->person->organisation_id)->type('extendsworkleave')->OnDate($model->start->format('Y-m-d H:i:s'))->orderby('started_at', 'desc')->first();
				if($extendpolicy)
				{
					$extends 	= $extendpolicy['value'];
				}
				else
				{
					$extends	= '+ 3 months';
				}

				$end 			= Carbon::create('end of December '.$model->start->format('Y').' '.$extends);
				$start 			= $model->start;
			}
			//2a. if not ::
			//- end was end of start year + 1 year
			//- start could be taken next year, anniversary of 1 year working
			else
			{
				$extendpolicy 	= \App\Models\Policy::organisationid($model->person->organisation_id)->type('extendsmidworkleave')->OnDate($model->start->format('Y-m-d H:i:s'))->orderby('started_at', 'desc')->first();
				if($extendpolicy)
				{
					$extends 	= $extendpolicy['value'];
				}
				else
				{
					$extends	= '+ 1 year';
				}

				$end 			= Carbon::create('end of December '.$model->start->format('Y').' '.$extends);
				$start 			= $start_work->copy()->addYear();
			}

			//3. check start work date

			//3a. if start work date after 15
			if($start_work->format('d') >= 15)
			{
				$next_start		= $start->copy()->addMonth();

				//3a1. if next start workleave bigger than end ::
				//-quota set zero
				if(!is_null($model->work->end) && $next_start->format('Y-m-d H:i:s') > $model->work->end->format('Y-m-d H:i:s'))
				{
					$quota 		= 0;
				}
				//3a1. if next start workleave less than end ::
				//- change start to next start
				//- quota equal 1
				else
				{
					$start 		= $next_start;
					$quota 		= 1;
				}
			}
			//3a. if start work date before 15
			else
			{
				$quota 			= 1;
			}

			//4. check if progressive workleave is end of year and quota over 12
			if((int)$start->format('m')==12 && $wleave_quota > 12)
			{
				$quota 			= $quota + ($wleave_quota - 12);
			}

			$model->start 		= $start->format('Y-m-d H:i:s');
			$model->end 		= $end->format('Y-m-d H:i:s');
			$model->quota 		= $quota;
		}
		//case CI
		elseif($model->work()->count() && $model->status == 'CI')
		{
			$quota 					= 0;
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
			
			$model->quota 			= $quota;
		}


        if($errors->count())
        {
			$model['errors']	= $errors;

        	return false;
        }

        return true;
	}

	/** 
     * observe GivenWorkleave event deleted
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
