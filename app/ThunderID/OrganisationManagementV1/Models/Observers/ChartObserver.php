<?php 

namespace App\ThunderID\OrganisationManagementV1\Models\Observers;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

use App\ThunderID\OrganisationManagementV1\Models\Chart;

/**
 * Used in Chart model
 *
 * @author cmooy
 */
class ChartObserver 
{
	/** 
	 * observe Chart event created
	 * 1. modify path
	 * 2. act, accept or refuse
	 * 
	 * @param $model
	 * @return bool
	 */
	public function created($model)
	{
		//1.modify path
		if($model->chart()->count())
		{
			$model->path           = $model->chart->path.','.$model->id;
		}
		else
		{
			$model->path           = $model->id;
		}

		if(!$model->save())
		{
			$model['errors']            = $model->getError();

			return false;
		}

		return true;
	}

	/** 
	 * observe Chart event saving
	 * 1. check parent
	 * 2. act, accept or refuse
	 * 
	 * @param $model
	 * @return bool
	 */
	public function saving($model)
	{
		$errors							= new MessageBag();

		if(is_null($model->id))
		{
			$id 						= 0;
		}
		else
		{
			$id 						= $model->id;
		}

		//1. check parent
		if($model->chart()->count())
		{
			//1a. check if parent is me
			if($model->chart_id == $id)
			{
				$errors->add('Chart', 'Parent tidak boleh sama dengan chart');
			}
			else
			{
				//1b. check if parent is my child
				$child							= Chart::orderBy('path','asc')
													->where('path','like',$model->path . ',%')
													->id($model->chart_id)
													->notid($id)
													->first();

				if($child)
				{
					$errors->add('Chart', 'Parent tidak boleh sama dengan turunan chart');
				}
			}
		}
		else
		{
			$model->chart_id 		= 0;
		}

		if($errors->count())
		{
			$model['errors'] 		= $errors;

			return false;
		}

		return true;
	}


	/** 
	 * observe Chart event updating
	 * 1. updated parent + child path
	 * 2. act, accept or refuse
	 * 
	 * @param $model
	 * @return bool
	 */
	public function updating($model)
	{
		//1. check parent
		if(isset($model->getDirty()['chart_id']) || !isset($model ->getDirty()['path']))
		{
			//1a. mengganti path
			if($model->chart()->count())
			{
				$model->path = $model->chart->path . "," . $model ->id;
			}
			else
			{
				$model->path = $model->id;
			}

			if(isset($model ->getOriginal()['path']))
			{
				//1b. mengganti semua path child
				$childs							= Chart::orderBy('path','asc')
													->where('path','like',$model->getOriginal()['path'] . ',%')
													->get();

				foreach ($childs as $child) 
				{
					$child->update(['path' => preg_replace('/'. $model ->getOriginal()['path'].',/', $model ->path . ',', $child->path,1)]);  
				}
			}
		}

		return true;
	}

	/** 
	 * observe Chart event deleting
	 * 1. Delete Child
	 * 2. act, accept or refuse
	 * 
	 * @param $model
	 * @return bool
	 */
	public function deleting($model)
	{
		$errors							= new MessageBag();
		
		//1. Delete Child
		$childs							= Chart::orderBy('path','desc')
											->where('path','like',$model->path . ',%')
											->get();

		foreach ($childs as $child) 
		{
			if(!$child->delete())
			{
				$errors->add('Chart', $child->getError());
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
