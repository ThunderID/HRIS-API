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
     * observe Employee event creating
     * 1. generate activation link
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function creating($model)
	{
		$errors								= new MessageBag();

		$model->activation_link		 		= md5(uniqid(rand(), TRUE));

        if($errors->count())
	    {
			$model['errors'] 				= $errors;

	    	return false;
	    }

        return true;
	}

	/** 
     * observe Employee event saving
     * 1. check need rehash
     * 2. unique username
     * 3. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function saving($model)
	{
		$errors									= new MessageBag();

		//1. check need rehash
		if (Hash::needsRehash($model->password))
        {
            $model->password					= bcrypt($model->password);
        }

		if(is_null($model->id))
		{
			$id 								= 0;
		}
		else
		{
			$id 								= $model->id;
		}

		//2. unique username
		if(!is_null($model->username))
		{
			$other_employee						= Employee::username($model->uniqid)->notid($id)->first();

			if($other_employee)
			{
				$errors->add('Employee', 'Username sudah terdaftar');
			}

	        if($errors->count())
	        {
				$model['errors'] 				= $errors;

	        	return false;
	        }
        }

        return true;
    }

	/** 
     * observe Employee event updating
     * 1. auto add last password updated at
     * 
     * @param $model
     * @return bool
     */
	public function updating($model)
	{
		//1. auto add last password updated at
		if(isset($model->getDirty()['password']))
		{
			$model->last_password_updated_at 	= Carbon::now()->format('Y-m-d H:i:s');
		}

		return true;
	}

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
