<?php 

namespace App\ThunderID\EmploymentSystemV1\Models\Observers;

use Illuminate\Support\MessageBag;
use Carbon\Carbon;
use Hash;

use App\ThunderID\EmploymentSystemV1\Models\Employee;

/**
 * Used in Employee model
 *
 * @author cmooy
 */
class EmployeeObserver 
{
	/** 
     * observe Employee event deleting
     * 1. delete document
     * 2. delete marital status
     * 3. delete relative
     * 4. delete contact
     * 5. delete work
     * 6. delete contract work
     * 7. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function deleting($model)
	{
		$errors						= new MessageBag();
		
		//1. delete document
		foreach ($model->persondocuments as $key => $value) 
		{
			if(!$value->delete())
			{
				$errors->add('Employee', $value->getError());
			}
		}

		//2. delete marital status
		foreach ($model->maritalstatuses as $key => $value) 
		{
			if(!$value->delete())
			{
				$errors->add('Employee', $value->getError());
			}
		}

		//3. delete relative
		foreach ($model->relatives as $key => $value) 
		{
			if(!$value->delete())
			{
				$errors->add('Employee', $value->getError());
			}
		}

		//4. delete contacts
		foreach ($model->contacts as $key => $value) 
		{
			if(!$value->delete())
			{
				$errors->add('Employee', $value->getError());
			}
		}

		//5. delete works
		foreach ($model->works as $key => $value) 
		{
			if(!$value->delete())
			{
				$errors->add('Employee', $value->getError());
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
