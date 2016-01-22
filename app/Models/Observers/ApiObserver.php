<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;

use App\Models\Api;

/**
 * Used in Api model
 *
 * @author cmooy
 */
class ApiObserver 
{
	/** 
     * observe Api event saving
     * 1. unique workstation address
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

		//1. unique workstation address
		$other_api 								= Api::address($model->workstation_address)->notid($id)->first();

		if($other_api)
		{
			$errors->add('Api', 'Workstation ID sudah terdaftar');
		}

        if($errors->count())
        {
			$model['errors'] 					= $errors;

        	return false;
        }

        return true;
	}
}
