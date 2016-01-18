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
     * observe Calendar event saving
     * 1. check workdays
     * 2. check readable days
     * 3. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function saving($model)
	{
		$errors					= new MessageBag();

		$workdays 				= explode(',', $model->workdays);

		//1. check workdays
		if(count($workdays) < 1)
		{
			$errors->add('Calendar', 'Format hari kerja dipisahkan tanda koma(,). Contoh : senin,selasa,..,sabtu');
		}

		//2. check readable days
		foreach ($workdays as $key => $value) 
		{
			if(!in_array($value, ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']))
			{
				$errors->add('Calendar', 'Format hari kerja dipisahkan tanda koma(,) dan harus sesuai dengan nama hari dalam bahasa Indonesia/Inggris');
			}
		}

        if($errors->count())
        {
			$model['errors'] 		= $errors;

        	return false;
        }

        return true;
	}

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
