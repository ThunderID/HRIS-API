<?php 

namespace App\ThunderID\OrganisationManagementV1\Models\Observers;

use App\Libraries\PolicyOfOrganisation as POO;

use Illuminate\Support\MessageBag;

/**
 * Used in Policy model
 *
 * @author cmooy
 */
class PolicyObserver 
{
	/** 
     * observe policy event updating
     * 1. validate saving
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function saving($model)
	{
		$errors 				= new MessageBag;

		//1. validate saving
		$validating_policy 		= new POO;

		if(!$validating_policy->validatesaving($model))
		{
			$errors->add('Policy', $validating_policy->getError());
		}
	
		if($errors->count())
		{
			$model['errors'] 		= $errors;

			return false;
		}

		return true;
	}

	/** 
     * observe policy event updating
     * 1. refuse changed
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function updating($model)
	{
		$errors 				= new MessageBag;

		//1. refuse changed
		$errors->add('Policy', 'Tidak dapat mengubah pengaturan kebijakan. Silahkan Buat kebijakan yang baru.');
	
		$model['errors'] 		= $errors;
		
		return false;
	}

	/** 
     * observe policy event deleting
     * 1. validate delete
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function deleting($model)
	{
		$errors 				= new MessageBag;

		//1. validate deleting
		$validating_policy 		= new POO;

		if(!$validating_policy->validatedeleting($model))
		{
			$errors->add('Policy', $validating_policy->getError());
		}

		if($errors->count())
		{
			$model['errors'] 		= $errors;

			return false;
		}
		
		return true;
	}
}
