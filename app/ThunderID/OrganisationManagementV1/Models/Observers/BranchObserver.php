<?php

namespace App\ThunderID\OrganisationManagementV1\Models\Observers;

use Illuminate\Support\MessageBag;

/**
 * Used in Branch model
 *
 * @author cmooy
 */
class BranchObserver 
{
	/** 
     * observe branch event created
     * 
     * @param $model
     * @return bool
     */
	public function created($model)
	{
		$errors					= new MessageBag();

        if($errors->count())
        {
			$model['errors']	= $errors;

        	return false;
        }

        return true;
	}

	/** 
     * observe branch event deleting
     * 1. delete chart
     * 2. delete contact
     * 3. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function deleting($model)
	{
		$errors						= new MessageBag();
		
		//1. delete chart
		foreach ($model->charts as $key => $value) 
		{
            if(!$value->delete())
            {
            	$errors->add('Branch', $value->getError());
            }
		}

		//2. delete contact
		foreach ($model->contacts as $key => $value) 
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
