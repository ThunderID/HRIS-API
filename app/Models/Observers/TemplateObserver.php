<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;

/**
 * Used in Template model
 *
 * @author cmooy
 */
class TemplateObserver 
{
	/** 
     * observe Template event deleting
     * 1. check documentdetails
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function deleting($model)
	{
		$errors						= new MessageBag();
		
		//1. check documentdetails
		if($model->documentdetails()->count())
		{
			$errors->add('Template', 'Tidak dapat menghapus dokumen yang berkaitan dengan karyawan atau yang memiliki template.');
		}
		else
		{
			foreach ($model->templates as $key => $value) 
			{
				if(!$value->delete())
				{
					$errors->add('Template', $delete->getError());
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
