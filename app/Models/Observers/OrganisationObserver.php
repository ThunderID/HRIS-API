<?php namespace App\Models\Observers;

use Illuminate\Support\MessageBag;
use Carbon\Carbon;

use \App\Models\Organisation;
use \App\Models\Branch;
use \App\Models\Chart;
use \App\Models\Work;

use \App\Models\Policy;

use \App\Models\Document;
use \App\Models\Template;

/**
 * Used in Organisation model
 *
 * @author cmooy
 */
class OrganisationObserver 
{
	/** 
     * observe organisation event created
     * 1. save default branch
     * 2. save default chart
     * 3. save default policy
     * 4. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function created($model)
	{
		$errors						= new MessageBag();

		//1. save default branch
		$branch 					= new Branch;

		$branch->fill([
			'name' 					=> 'Pusat',
			'organisation_id'		=> $model['id'],
		]);

		if(!$branch->save())
		{
			$errors->add('Organisation', $branch->getError());
		}

		//2. save default chart
		if(!$errors->count())
		{
			$chart					= new Chart;

			$chart->fill([
				'branch_id' 		=> $branch['id'],
				'name' 				=> 'system admin',
				'grade' 			=> 'A',
				'tag' 				=> 'admin',
				'min_employee' 		=> '1',
				'ideal_employee' 	=> '1',
				'max_employee' 		=> '1',
				'current_employee' 	=> '1',
			]);

			if(!$chart->save())
			{
				$errors->add('Organisation', $chart->getError());
			}
		}

		//3. save default policy
		if(!$errors->count())
		{
			$types					= ['passwordreminder', 'assplimit', 'ulsplimit', 'hpsplimit', 'htsplimit', 'hcsplimit', 'firststatussettlement', 'secondstatussettlement', 'firstidle', 'secondidle','thirdidle', 'extendsworkleave', 'extendsmidworkleave', 'firstacleditor', 'secondacleditor', 'asid', 'ulid', 'hcid', 'htid', 'hpid'];
			$values					= ['- 3 months', '1', '1', '2', '2', '2', '- 1 month', '- 5 days', '900', '3600','7200', '+ 3 months', '+ 1 year + 3 months', '- 1 month', '- 5 days', 0, 0, 0, 0, 0];

			foreach(range(0, count($types)-1) as $key => $index)
			{
				$policy				= new Policy;
				$policy->fill([
					'type'			=> $types[$key],
					'value'			=> $values[$key],
					'started_at'	=> Carbon::now()->format('Y-m-d H:i:s'),
				]);

				$policy->organisation()->associate($model);

				if (!$policy->save())
				{
					$errors->add('Organisation', $policy->getError());
				}
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
     * observe organisation event saving
     * 1. check unique code
     * 2. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function saving($model)
	{
		$errors						= new MessageBag();
		
		//1. check unique code
        $code						= Organisation::code($model->code)->notid($model->id)->first();

        if(!is_null($code))
        {
			$errors->add('Organisation', 'Organisation sudah terdaftar.');
        }
		        
        if($errors->count())
        {
			$model['errors'] 		= $errors;

        	return false;
        }

        return true;
	}

	/** 
     * observe organisation event deleting
     * 1. check calendar
     * 2. check cuti
     * 3. check karyawan
     * 4. check dokumen karyawan
     * 5. delete branch
     * 6. delete policy
     * 7. delete dokumen
     * 8. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function deleting($model)
	{
		$errors						= new MessageBag();
		
		//1. check calendar
		if($model->calendars()->count())
		{
			$errors->add('Organisation', 'Tidak dapat menghapus organisasi yang memiliki data kalender. Silahkan hapus data kalender terlebih dahulu.');
		}

		//2. check cuti
		if($model->workleaves()->count())
		{
			$errors->add('Organisation', 'Tidak dapat menghapus organisasi yang memiliki data cuti. Silahkan hapus data cuti terlebih dahulu.');
		}

		//3. check karyawan
		if($model->employees()->count())
		{
			$errors->add('Organisation', 'Tidak dapat menghapus organisasi yang memiliki pegawai. Silahkan hapus data pegawai terlebih dahulu.');
		}

		//4. check dokumen karyawan
		$personsdocs 			= Document::organisationid($model['attributes']['id'])->wherehas('persons', function($q){$q;})->count();

		if($personsdocs)
		{
			$errors->add('Organisation', 'Tidak dapat menghapus organisasi yang memiliki dokumen personalia. Silahkan hapus data dokumen personalia terlebih dahulu.');
		}

		//5. delete branch
		foreach ($model->branches as $key => $value) 
		{
			//5a. delete chart
			foreach ($value->charts as $key2 => $value2) 
			{
				//5ai. delete work
				foreach ($value2->works as $key3 => $value3) 
				{
					$work 	 				= new Work;
					$delete 				= $work->find($value3['pivot']['id']);

					if($delete && !$delete->delete())
					{
						$errors->add('Organisation', $delete->getError());
					}
				}

				$chart 	 					= new Chart;
				$delete 					= $chart->id($value2->id)->first();
				if($delete && !$delete->delete())
				{
					$errors->add('Organisation', $delete->getError());
				}
			}

			$branch 	 					= new Branch;
			$delete 						= $branch->id($value->id)->first();
			if($delete && !$delete->delete())
			{
				$errors->add('Organisation', $delete->getError());
			}
		}

		//6. delete policy
		foreach ($model->policies as $key => $value) 
		{
			$policy 	 					= new Policy;
			$delete 						= $policy->id($value->id)->first();
			if($delete && !$delete->delete())
			{
				$errors->add('Organisation', $delete->getError());
			}
		}

		//7. delete dokumen
		foreach ($model->documents as $key => $value) 
		{
			//7a. delete template dokumen
			foreach ($value->templates as $key2 => $value2) 
			{
				$template 	 				= new Template;
				$delete 					= $template->id($value2->id)->first();
				if($delete && !$delete->delete())
				{
					$errors->add('Organisation', $delete->getError());
				}
			}

			$document 	 					= new Document;
			$delete 						= $document->id($value->id)->first();
			if($delete && !$delete->delete())
			{
				$errors->add('Organisation', $delete->getError());
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
