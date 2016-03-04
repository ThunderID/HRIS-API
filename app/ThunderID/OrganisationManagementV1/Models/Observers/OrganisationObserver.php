<?php 

namespace App\ThunderID\OrganisationManagementV1\Models\Observers;

use Illuminate\Support\MessageBag;
use Carbon\Carbon;

use \App\ThunderID\OrganisationManagementV1\Models\Organisation;
use \App\ThunderID\OrganisationManagementV1\Models\Branch;
use \App\ThunderID\OrganisationManagementV1\Models\Chart;
use \App\ThunderID\OrganisationManagementV1\Models\Policy;

/**
 * Used in Organisation model
 *
 * @author cmooy
 */
class OrganisationObserver 
{
	/** 
     * observe organisation event created
     * 
     * @param $model
     * @return bool
     */
	public function created($model)
	{
		$errors						= new MessageBag();

        if($errors->count())
        {
			$model['errors'] 		= $errors;

        	return false;
        }

        return true;
	}

	/** 
     * observe organisation event saving
     * 1. check unique code
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function saving($model)
	{
		$errors									= new MessageBag();

		if(is_null($model->id))
		{
			$id 								= 0;
		}
		else
		{
			$id 								= $model->id;
		}

		//1. check unique code
        $code									= Organisation::code($model->code)->notid($id)->first();

        if(!is_null($code))
        {
			$errors->add('Organisation', 'Organisation sudah terdaftar.');
        }
		        
        if($errors->count())
        {
			$model['errors'] 		= $errors;

        	return false;
        }

        return true;
	}

	/** 
     * observe organisation event deleting
     * 1. delete branch
     * 2. delete chart
     * 3. delete policy
     * 4. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function deleting($model)
	{
		$errors								= new MessageBag();
		
		//1. delete branch
		foreach ($model->branches as $key => $value) 
		{
			//2. delete chart
			foreach ($value->charts as $key2 => $value2) 
			{
				$chart 	 					= new Chart;
				$delete 					= $chart->id($value2->id)->first();
				if($delete && !$delete->delete())
				{
					$errors->add('Organisation', $delete->getError());
				}
			}

			$branch 	 					= new Branch;
			$delete 						= $branch->id($value->id)->first();
			if($delete && !$delete->delete())
			{
				$errors->add('Organisation', $delete->getError());
			}
		}

		//3. delete policy
		foreach ($model->policies as $key => $value) 
		{
			$policy 	 					= new Policy;
			$delete 						= $policy->id($value->id)->first();
			if($delete && !$delete->delete())
			{
				$errors->add('Organisation', $delete->getError());
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
