<?php

namespace App\ThunderID\OrganisationManagementV1\Models\Observers;

use Illuminate\Support\MessageBag;

use App\ThunderID\OrganisationManagementV1\Models\Contact;

/**
 * Used in Contact model
 *
 * @author cmooy
 */
class ContactObserver 
{
	/** 
	 * observe contact event saving
	 * 1. check default contact to set init default
	 * 2. act, accept or refuse
	 * 
	 * @param $model
	 * @return bool
	 */
	public function saving($model)
	{
		$countcontact		= Contact::contactableid($model->contactable_id)
									->contactabletype($model->contactable_type)
									->default(true)
									->count();
		
		if($countcontact == 0)
		{
			$model->is_default     = 1;
		}

		return true;
	}

	/** 
	 * observe Contact event saved
	 * 1. check default Contact and make sure it's the only default
	 * 2. act, accept or refuse
	 * 
	 * @param $model
	 * @return bool
	 */
	public function saved($model)
	{
		//1. check default Contact event
		$contacts				= Contact::contactableid($model->contactable_id)
									->contactabletype($model->contactable_type)
									->default(true)
									->type($model->type)
									->notid($model->id)
									->get();

		foreach ($contacts as $contact) 
		{
			//1a. set is_default to false for other Contact
		   $contact->is_default = false;

		   if(!$contact->save())
		   {
				$model['errors']    = $contact->getError();

				return false;
		   }
		}

		return true;
	}
}
