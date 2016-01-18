<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;

use App\Models\Contact;
use App\Models\FingerPrint;

/**
 * Used in Policy model
 *
 * @author cmooy
 */
class PolicyObserver 
{
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
		$errors->add('policy', 'Tidak dapat mengubah pengaturan kebijakan. Silahkan Buat kebijakan yang baru.');
	
		$model['errors'] 		= $errors;
		
		return false;
	}

	/** 
     * observe policy event deleting
     * 1. refuse changed
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function deleting($model)
	{
		$errors 				= new MessageBag;

		//1. refuse changed
		$errors->add('policy', 'Tidak dapat menghapus pengaturan kebijakan. Silahkan Buat kebijakan yang baru.');
	
		$model['errors'] 		= $errors;
		
		return false;
	}
}
