<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;

use App\Models\PersonContact;

/**
 * Used in PersonContact model
 *
 * @author cmooy
 */
class PersonContactObserver 
{
	/** 
     * observe PersonContact event saving
     * 1. check item email
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function saving($model)
	{
		$errors					= new MessageBag();

		// 1. check item email
		if($model->item=='email' && $model->person()->count())
		{
			$other_person 		= PersonContact::item('email')->notpersonid($model->person_id)->value($model->value)->first();

			if($other_person)
			{
				$errors->add('Email', 'Email sudah terdaftar sebagai milik '.$other_person->person->name);
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
