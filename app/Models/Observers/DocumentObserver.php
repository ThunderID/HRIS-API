<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;

/**
 * Used in Document model
 *
 * @author cmooy
 */
class DocumentObserver 
{
	/** 
     * observe Document event deleting
     * 1. check persondocuments
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function deleting($model)
	{
		//1. check persondocuments
		if($model->persondocuments()->count())
		{
			$errors->add('Document', 'Tidak dapat menghapus dokumen yang berkaitan dengan karyawan atau yang memiliki template.');
		}
		else
		{
			foreach ($model->templates as $key => $value) 
			{
				if(!$value->delete())
				{
					$errors->add('Document', $delete->getError());
				}
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
