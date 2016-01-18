<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;

/**
 * Used in Workleave model
 *
 * @author cmooy
 */
class WorkleaveObserver 
{
	/** 
     * observe Workleave event updating
     * 1. cek quota
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function updating($model)
	{
		//1. cek quota
		if(isset($model->getDirty()['quota']))
		{
			$errors 			= new MessageBag;

			$errors->add('quota', 'Perubahan hak cuti untuk karyawan tidak dapat dilakukan. Silahkan tambahkan template cuti yang baru.');
		}

        if($errors->count())
        {
			$model['errors'] 		= $errors;

        	return false;
        }

        return true;
	}

	/** 
     * observe Workleave event deleting
     * 1. check followed works
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function deleting($model)
	{
		$errors 				= new MessageBag;

		//1. check followed works
		if($model->followedworks()->count())
		{
			$errors->add('Workleave', 'Tidak dapat menghapus data cuti yang menjadi acuan cuti karyawan. Silahkan non aktif kan data cuti yang tidak berlaku lagi.');
		}

        if($errors->count())
        {
			$model['errors'] 		= $errors;

        	return false;
        }

        return true;
	}
}
