<?php namespace App\Models\Observers;

use \Illuminate\Support\MessageBag as MessageBag;

/**
 * Used in PersonSchedule model
 *
 * @author cmooy
 */
class PersonScheduleObserver 
{
	/** 
     * observe personschedule event saved
     * 1. check employee on that day logs
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function saved($model)
	{
		$errors					= new MessageBag;

		//1. check employee on that day logs
		if($model->person()->count())
		{
			$log				= \App\Models\Log::ondate($model->on->format('Y-m-d'))->personid($model->person_id)->first();

			if($log)
			{
				$log->created_by 		= $model->created_by;

				if(!$log->save())
				{
					$errors->add('Log', $log->getError());
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
     * observe personschedule event deleted
     * 1. check employee on that day logs
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
			$log				= \App\Models\Log::ondate($model->on->format('Y-m-d'))->personid($model->person_id)->first();

			if($log)
			{
				$log->created_by 		= $model->created_by;

				if(!$log->save())
				{
					$errors->add('Log', $log->getError());
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
