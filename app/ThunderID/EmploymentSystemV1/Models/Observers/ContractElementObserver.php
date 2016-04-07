<?php 

namespace App\ThunderID\EmploymentSystemV1\Models\Observers;

use Illuminate\Support\MessageBag;

/**
 * Used in ContractElement model
 *
 * @author cmooy
 */
class ContractElementObserver 
{
	/** 
     * observe Employee event deleting
     * 1. check contract works
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function deleting($model)
	{
		$errors						= new MessageBag();
		
		//1. check contract works
		if($model->contractworks->count())
		{
			$errors->add('ContractElement', 'Tidak dapat menghapus element kontrak yang sudah / sedang dipakai dalam kontrak kerja karyawan.');
		}

		if($errors->count())
		{
			$model['errors'] 		= $errors;

			return false;
		}

		return true;
	}
}
