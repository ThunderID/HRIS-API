<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;

use App\Models\Document;

/**
 * Used in PersonDocument model
 *
 * @author cmooy
 */
class PersonDocumentObserver 
{
	/** 
     * observe PersonDocument event saving
     * 1. check document required
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function saving($model)
	{
		$errors					= new MessageBag();

		// 1. check document required
		if($model->document()->count())
		{
			$check_document		= Document::id($model->document_id)->isrequired($model->required)->first();

			if($check_document)
			{
				$errors->add('Document', 'Dokumen tidak valid');
			}
		}

		if($errors->count())
		{
			$model['errors'] 		= $errors;

			return false;
		}

		return true;
	}

	/** 
     * observe PersonDocument event deleting
     * 1. delete document detail
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function deleting($model)
	{
		$errors					= new MessageBag();

	    //1. delete document detail
		foreach ($model->documentdetails as $key => $value) 
		{
			if(!$value->delete())
            {
            	$errors->add('Document', $value->getError());
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
