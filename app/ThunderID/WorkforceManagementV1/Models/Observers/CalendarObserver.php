<?php 

namespace App\ThunderID\WorkforceManagementV1\Models\Observers;

use Illuminate\Support\MessageBag;
use App\ThunderID\WorkforceManagementV1\Models\CalendarWork;

/**
 * Used in Calendar model
 *
 * @author cmooy
 */
class CalendarObserver 
{
	/** 
	 * observe Calendar event deleting
	 * 1. delete schedule
	 * 2. delete calendar work
	 * 3. act, accept or refuse
	 * 
	 * @param $model
	 * @return bool
	 */
	public function deleting($model)
	{
		$errors					= new MessageBag();

		//1. delete schedule
		foreach ($model->schedules as $key => $value) 
		{
			if(!$value->delete())
			{
				$errors->add('Calendar', $value->getError());
			}
		}
		
		//2. delete calendar work
		$works 					= CalendarWork::calendarid($model->id)->get();

		foreach ($works as $key => $value) 
		{
			if(!$value->delete())
			{
				$errors->add('Calendar', $value->getError());
			}
		}

		if($errors->count())
		{
			$model['errors'] 		= $errors;

			return false;
		}

		return true;
	}
}
