<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

use App\Models\Contact;

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
		//1. check default contact to set init default
		if(isset($model->person_id))
		{
			$countcontact	= Contact::where('person_id', $model->person_id)
											->whereIn('person_type', $model->person_type)
											->where('is_default', 1)
											->count();
			if($countcontact == 0)
			{
				$model->is_default     = 1;
			}
		}

		elseif(isset($model->branch_id))
		{
			$countcontact	= Contact::where('branch_id', $model->branch_id)
											->where('branch_type', $model->branch_type)
											->where('is_default', 1)
											->count();
			if($countcontact == 0)
			{
				$model->is_default     = 1;
			}
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
		if(isset($model->person_id) && $model->is_default == 1)
		{
			$contacts	= Contact::where('person_id', $model->person_id)
										->whereIn('person_type', $model->person_type)
										->where('is_default', 1)
										->notid($model->id)
										->get();

			foreach ($contacts as $contact) 
			{
				//1a. set is_default to false for other Contact
			   $contact->fill([
					'is_default'        => 0,
				]);

			   if(!$contact->save())
			   {
					$model['errors']    = $contact->getError();

					return false;
			   }
			}
		}

		elseif(isset($model->branch_id) && $model->is_default == 1)
		{
			$contacts	= Contact::where('branch_id', $model->branch_id)
										->where('branch_type', $model->branch_type)
										->where('is_default', 1)
										->notid($model->id)
										->get();

			foreach ($contacts as $contact) 
			{
				//1a. set is_default to false for other Contact
			   $contact->fill([
					'is_default'        => 0,
				]);

			   if(!$contact->save())
			   {
					$model['errors']    = $contact->getError();

					return false;
			   }
			}
		}
		return true;
	}
}
