<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;
use Carbon\Carbon;
use Hash;

use App\Models\Employee;
use App\Models\MaritalStatus;
use App\Models\PersonDocument;
use App\Models\DocumentDetail;
use App\Models\Work;
use App\Models\PersonWorkleave;
use App\Models\PersonSchedule;
use App\Models\PersonContact;

/**
 * Used in Employee model
 *
 * @author cmooy
 */
class EmployeeObserver 
{
	/** 
     * observe Employee event creating
     * 1. auto generate nik
     * 2. auto generate username
     * 3. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function creating($model)
	{
		//1. auto generate nik
		$model->uniqid 							= $model->generateNIK($model);

		//2. auto generate username
		$model->username						= $model->generateUsername($model);

		return true;
	}

	/** 
     * observe Employee event saving
     * 1. check need rehash
     * 2. auto add last password updated at
     * 3. unique nik
     * 4. unique username
     * 5. act, accept or refuse
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

		//2. auto add last password updated at
		if(isset($model->getDirty()['last_password_updated_at']))
		{
			$model->last_password_updated_at 	= Carbon::now()->format('Y-m-d H:i:s');
		}

		if(is_null($model->id))
		{
			$id 								= 0;
		}
		else
		{
			$id 								= $model->id;
		}

		//3. unique nik
		$other_employee 						= Employee::nik($model->uniqid)->notid($id)->first();

		if($other_employee)
		{
			$errors->add('Employee', 'NIK sudah terdaftar');
		}

		//4. unique username
		$other_employee 						= Employee::username($model->uniqid)->notid($id)->first();

		if($other_employee)
		{
			$errors->add('Employee', 'Username sudah terdaftar');
		}

        if($errors->count())
        {
			$model['errors'] 					= $errors;

        	return false;
        }

        return true;
	}

	/** 
     * observe Employee event deleting
     * 1. check logs
     * 2. delete marital status
     * 3. delete document
     * 4. delete work
     * 5. delete workleave
     * 6. delete schedule
     * 7. delete contact
     * 8. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function deleting($model)
	{
		$errors						= new MessageBag();
		
		//1. check calendar
		if($model->logs()->count())
		{
			$errors->add('Employee', 'Tidak dapat menghapus karyawan yang memiliki data log.');
		}

		//2. delete marital status
		foreach ($model->maritalstatuses as $key => $value) 
		{
			$ms 	 						= new MaritalStatus;
			$delete 						= $ms->id($value->id)->first();
			if($delete && !$delete->delete())
			{
				$errors->add('Employee', $delete->getError());
			}
		}

		//3. delete document
		foreach ($model->persondocuments as $key => $value) 
		{
			//7a. delete detail documents
			foreach ($value->documentdetails as $key2 => $value2) 
			{
				$dd 		 				= new DocumentDetail;
				$delete 					= $dd->id($value2->id)->first();
				if($delete && !$delete->delete())
				{
					$errors->add('Employee', $delete->getError());
				}
			}

			$document 	 					= new PersonDocument;
			$delete 						= $document->id($value->id)->first();
			if($delete && !$delete->delete())
			{
				$errors->add('Employee', $delete->getError());
			}
		}

		//4. delete works
		foreach ($model->works as $key => $value) 
		{
			$work 	 						= new Work;
			$delete 						= $work->id($value->id)->first();
			if($delete && !$delete->delete())
			{
				$errors->add('Employee', $delete->getError());
			}
		}

		//5. delete workleaves
		foreach ($model->personworkleaves as $key => $value) 
		{
			$pw 	 						= new PersonWorkleave;
			$delete 						= $pw->id($value->id)->first();
			if($delete && !$delete->delete())
			{
				$errors->add('Employee', $delete->getError());
			}
		}

		//6. delete schedules
		foreach ($model->personschedules as $key => $value) 
		{
			$ps 	 						= new PersonSchedule;
			$delete 						= $ps->id($value->id)->first();
			if($delete && !$delete->delete())
			{
				$errors->add('Employee', $delete->getError());
			}
		}

		//7. delete contacts
		foreach ($model->contacts as $key => $value) 
		{
			$pc 	 						= new PersonContact;
			$delete 						= $pc->id($value->id)->first();
			if($delete && !$delete->delete())
			{
				$errors->add('Employee', $delete->getError());
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
