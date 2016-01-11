<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;

use App\Models\Contact;
use App\Models\FingerPrint;

/**
 * Used in Branch model
 *
 * @author cmooy
 */
class BranchObserver 
{
	/** 
     * observe branch event created
     * 1. save default finger print
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function created($model)
	{
		$errors					= new MessageBag();

		//1. save default finger print
		$finger					= new FingerPrint;
		
		$finger->fill([
			'branch_id'			=> $model['id'],
		]);

		if(!$finger->save())
		{
			$errors->add('Branch', $finger->getError());
		}
		        
        if($errors->count())
        {
			$model['errors'] 		= $errors;

        	return false;
        }

        return true;
	}

	/** 
     * observe branch event deleting
     * 1. save default finger print
     * 2. delete contact
     * 3. delete finger
     * 4. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function deleting($model)
	{
		//1. check chart
		if($model->charts()->count())
		{
			$errors->add('Branch', 'Tidak dapat menghapus cabang yang memiliki departemen.');
		}

		//2. delete contact
		foreach ($model->contacts as $key => $value) 
		{
            if(!$value->delete())
            {
            	$errors->add('Branch', $value->getError());
            }
		}

		//3. delete finger
		foreach ($model->fingers as $key => $value) 
		{
            if(!$value->delete())
            {
            	$errors->add('Branch', $value->getError());
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
