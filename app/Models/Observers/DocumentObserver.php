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
     * 1. delete person documents
     * 2. delete document template
     * 3. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function deleting($model)
	{
		$errors 			= new MessageBag;
		
		//1. delete person documents
		foreach ($model->persondocuments as $key => $value) 
		{
			if(!$value->delete())
			{
				$errors->add('Document', $delete->getError());
			}
		}
		
		//2. delete document template
		foreach ($model->templates as $key => $value) 
		{
			if(!$value->delete())
			{
				$errors->add('Document', $delete->getError());
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
