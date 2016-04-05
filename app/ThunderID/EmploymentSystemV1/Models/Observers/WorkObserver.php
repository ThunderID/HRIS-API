<?php 

namespace App\ThunderID\EmploymentSystemV1\Models\Observers;

use Illuminate\Support\MessageBag;
use Carbon\Carbon;
use Hash;

use App\ThunderID\EmploymentSystemV1\Models\Work;

/**
 * Used in Work model
 *
 * @author cmooy
 */
class WorkObserver 
{
	/** 
     * observe Work event deleting
     * 1. delete contract
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function deleting($model)
	{
		$errors						= new MessageBag();
		
		//1. delete contract
		foreach ($model->contractworks as $key => $value) 
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
