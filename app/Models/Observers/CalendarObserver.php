<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;

/**
 * Used in Calendar model
 *
 * @author cmooy
 */
class CalendarObserver 
{
	/** 
     * observe Calendar event deleting
     * 1. check child
     * 2. check chart
     * 3. check schedule
     * 4. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function deleting($model)
	{
		$errors					= new MessageBag();

		//1. check child
		if($model->childs()->count())
		{
			$errors->add('Calendar', 'Tidak dapat menghapus kalender yang diikuti oleh kalender lain.');
		}

		//2. check chart
		if($model->charts()->count())
		{
			$errors->add('Calendar', 'Tidak dapat menghapus kalender yang berkaitan dengan karyawan.');
		}

		//3. check schedule
		if($model->schedules()->count())
		{
			$errors->add('Calendar', 'Tidak dapat menghapus kalender yang memiliki jadwal.');
		}

        if($errors->count())
        {
			$model['errors'] 		= $errors;

        	return false;
        }

        return true;
	}
}
