<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

use App\Models\Chart;

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
		if(isset($model->getDirty()['category_id']) || !isset($model ->getDirty()['path']))
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
				$childs                         = Chart::orderBy('path','asc')
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
	 * 1. Delete Follow workleave
	 * 2. Delete Follow calendar
	 * 3. Delete Child
	 * 4. act, accept or refuse
	 * 
	 * @param $model
	 * @return bool
	 */
	public function deleting($model)
	{
		$errors							= new MessageBag();
		//1. Delete Follow workleave
		foreach ($model->chartworkleaves as $key => $value) 
		{
			if(!$value->delete())
			{
				$errors->add('Chart', $value->getError());
			}
		}

		//2. Delete Follow calendar
		foreach ($model->follows as $key => $value) 
		{
			if(!$value->delete())
			{
				$errors->add('Chart', $value->getError());
			}
		}
		
		//3. Delete Child
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
