<?php namespace App\Models\Observers;

use \Illuminate\Support\MessageBag as MessageBag;

/**
 * Used in Schedule model
 *
 * @author cmooy
 */
class ScheduleObserver 
{
	/** 
     * observe schedule event saving
     * 1. check duplicate schedule
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function saving($model)
	{
		$errors				= new MessageBag;

		if(is_null($model->id))
		{
			$id 			= 0;
		}
		else
		{
			$id 			= $model->id;
		}

		//1. check duplicate schedule
		if($model->calendar()->count())
		{
			$other_schedule	= \App\Models\Schedule::ondate($model->on->format('Y-m-d H:i:s'))->notid($id)->first();

			if($other_schedule)
			{
				$errors->add('ondate', 'Tidak dapat menyimpan dua jadwal di hari yang sama. Silahkan edit jadwal sebelumnya tambahkan jadwal khusus pada karyawan yang bersangkutan.');
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
     * observe schedule event saved
     * 1. check employee doesn't have schedule on the day
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function saved($model)
	{
		$errors					= new MessageBag;

		//1. check employee doesn't have schedule on the day
		if($model->calendar()->count())
		{
			$logs				= \App\Models\Log::ondate($model->on->format('Y-m-d'))->JoinPersonHasNoScheduleOn($model->on)->joinworkfrompersonon($model->on)->groupby('logs.person_id')->get();

			if($logs->count())
			{
				foreach ($logs as $key => $value) 
				{
					//update schedule 
					$value->created_by 		= $model->created_by;

					if(!$value->save())
					{
						$errors->add('Log', $value->getError());
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
     * observe schedule event deleted
     * 1. check employee doesn't have schedule on the day
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function deleted($model)
	{
		$errors					= new MessageBag;

		//1. check employee doesn't have schedule on the day
		if($model->calendar()->count())
		{
			$logs				= \App\Models\Log::ondate($model->on->format('Y-m-d'))->JoinPersonHasNoScheduleOn($model->on)->joinworkfrompersonon($model->on)->groupby('logs.person_id')->first();

			if($logs->count())
			{
				foreach ($logs as $key => $value) 
				{
					//update schedule 
					$value->created_by 		= $model->created_by;

					if(!$value->save())
					{
						$errors->add('Log', $value->getError());
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
