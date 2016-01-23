<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * Handle Protected Resource of EmployeeDocument
 * 
 * @author cmooy
 */
class EmployeeDocumentController extends Controller
{
	/**
	 * Display all EmployeeDocuments
	 *
	 * @param search, skip, take
	 * @return JSend Response
	 */
	public function index($org_id = null, $employ_id = null)
	{
		$employee 					= \App\Models\Employee::organisationid($org_id)->id($employ_id)->first();

		if(!$employee)
		{
			return new JSend('error', (array)Input::all(), 'Karyawan tidak valid.');
		}

		$result						= \App\Models\EmploymentDocument::personid($employ_id);

		if(Input::has('search'))
		{
			$search					= Input::get('search');

			foreach ($search as $key => $value) 
			{
				switch (strtolower($key)) 
				{
					default:
						# code...
						break;
				}
			}
		}

		$count						= $result->count();

		if(Input::has('skip'))
		{
			$skip					= Input::get('skip');
			$result					= $result->skip($skip);
		}

		if(Input::has('take'))
		{
			$take					= Input::get('take');
			$result					= $result->take($take);
		}

		$result						= $result->with(['document', 'documentdetails', 'documentdetails.template'])->get()->toArray();

		return new JSend('success', (array)['count' => $count, 'data' => $result]);
	}

	/**
	 * Display a EmployeeDocument of a employee
	 *
	 * @return Response
	 */
	public function detail($org_id = null, $employ_id = null, $id = null)
	{
		$result						= \App\Models\EmploymentDocument::id($id)->personid($employ_id)->with(['document', 'documentdetails', 'documentdetails.template'])->first();

		if($result)
		{
			return new JSend('success', (array)$result->toArray());
		}

		return new JSend('error', (array)Input::all(), 'ID Tidak Valid.');
	}

	/**
	 * Store a EmployeeDocument (throw to queue)
	 * 
	 * 1. store EmployeeDocument of employee to queue
	 * 2. store EmployeeDocument of employees to queue
	 * @return JSend Response
	 */
	public function store($org_id = null, $employ_id = null)
	{
		if(!Input::has('EmployeeDocument'))
		{
			return new JSend('error', (array)Input::all(), 'Tidak ada data EmployeeDocument.');
		}

		$errors						= new MessageBag();

		DB::beginTransaction();

		//1. store EmployeeDocument of employee to queue
		$EmployeeDocument						= Input::get('EmployeeDocument');

		if(is_null($EmployeeDocument['id']))
		{
			$is_new					= true;
		}
		else
		{
			$is_new					= false;
		}

		$EmployeeDocument_rules				=	[
											'employee_id'					=> 'required|exists:tmp_employees,id',
											'name'							=> 'required|max:255',
											'status'						=> 'required|in:DN,CB,UL,HB,L',
											'on'							=> 'required|date_format:"Y-m-d H:i:s"',
											'start'							=> 'required|date_format:"H:i:s"',
											'end'							=> 'required|date_format:"H:i:s"',
										];

		//1a. Validate Basic EmployeeDocument Parameter
		$parameter 					= $EmployeeDocument;
		unset($parameter['employee']);

		$validator					= Validator::make($parameter, $EmployeeDocument_rules);

		if (!$validator->passes())
		{
			$errors->add('EmployeeDocument', $validator->errors());
		}
		else
		{
			$total 						= \App\Models\Work::personid($employ_id)->count();

			$queue 						= new \App\Models\Queue;
			$queue->fill([
					'process_name' 			=> 'hr:EmployeeDocuments',
					'process_option' 		=> 'store',
					'parameter' 			=> json_encode($parameter),
					'total_process' 		=> $total,
					'task_per_process' 		=> 1,
					'process_number' 		=> 0,
					'total_task' 			=> $total,
					'message' 				=> 'Initial Commit',
				]);

			if(!$queue->save())
			{
				$errors->add('EmployeeDocument', $queue->getError());
			}
		}
		//End of validate EmployeeDocument

		//2. store EmployeeDocument of employees to queue
		if(!$errors->count() && isset($EmployeeDocument['employee']['employees']) && is_array($EmployeeDocument['employee']['employees']))
		{
			foreach ($EmployeeDocument['employee']['employees'] as $key => $value) 
			{
				$cals_data						= \App\Models\Employee::id($value['id'])->personid($employ_id)->first();

				if(!$cals_data)
				{
					$errors->add('employee', 'Tidak ada Karyawan '.$value['name']);
				}

				if(!$errors->count())
				{
					$total 						= \App\Models\Work::personid($value['id'])->count();
					$parameter['employee_id']	= $value['id'];

					$queue 						= new \App\Models\Queue;
					$queue->fill([
							'process_name' 			=> 'hr:EmployeeDocuments',
							'process_option' 		=> 'store',
							'parameter' 			=> json_encode($parameter),
							'total_process' 		=> $total,
							'task_per_process' 		=> 1,
							'process_number' 		=> 0,
							'total_task' 			=> $total,
							'message' 				=> 'Initial Commit',
						]);

					if(!$queue->save())
					{
						$errors->add('EmployeeDocument', $queue->getError());
					}
				}
			}
		}
		//End of validate employee EmployeeDocument

		if($errors->count())
		{
			DB::rollback();

			return new JSend('error', (array)Input::all(), $errors);
		}

		DB::commit();
		
		return new JSend('success', (array)Input::all());
	}


	/**
	 * Delete a EmployeeDocument (throw to queue)
	 *
	 * @return Response
	 */
	public function delete($org_id = null, $employ_id = null, $id = null)
	{
		//
		$EmployeeDocument					= \App\Models\EmploymentDocument::id($id)->personid($employ_id)->with(['document', 'documentdetails', 'documentdetails.template'])->first();

		if(!$EmployeeDocument)
		{
			return new JSend('error', (array)Input::all(), 'Jadwal tidak ditemukan.');
		}

		$errors						= new MessageBag();

		DB::beginTransaction();

		//1. store EmployeeDocument of employee to queue
		$EmployeeDocument					= $EmployeeDocument->toArray();

		if(is_null($EmployeeDocument['id']))
		{
			$is_new					= true;
		}
		else
		{
			$is_new					= false;
		}

		$EmployeeDocument_rules				=	[
											'employee_id'					=> 'required|exists:tmp_employees,id',
											'name'							=> 'required|max:255',
											'status'						=> 'required|in:DN,CB,UL,HB,L',
											'on'							=> 'required|date_format:"Y-m-d H:i:s"',
											'start'							=> 'required|date_format:"H:i:s"',
											'end'							=> 'required|date_format:"H:i:s"',
										];

		//1a. Validate Basic EmployeeDocument Parameter
		$parameter 					= $EmployeeDocument;
		unset($parameter['employee']);

		$validator					= Validator::make($parameter, $EmployeeDocument_rules);

		if (!$validator->passes())
		{
			$errors->add('EmployeeDocument', $validator->errors());
		}
		else
		{
			$total 						= \App\Models\Work::personid($employ_id)->count();

			$queue 						= new \App\Models\Queue;
			$queue->fill([
					'process_name' 			=> 'hr:EmployeeDocuments',
					'process_option' 		=> 'delete',
					'parameter' 			=> json_encode($parameter),
					'total_process' 		=> $total,
					'task_per_process' 		=> 1,
					'process_number' 		=> 0,
					'total_task' 			=> $total,
					'message' 				=> 'Initial Commit',
				]);

			if(!$queue->save())
			{
				$errors->add('EmployeeDocument', $queue->getError());
			}
		}
		//End of validate EmployeeDocument

		//2. store EmployeeDocument of employees to queue
		if(!$errors->count() && isset($EmployeeDocument['employee']['employees']) && is_array($EmployeeDocument['employee']['employees']))
		{
			foreach ($EmployeeDocument['employee']['employees'] as $key => $value) 
			{
				$cals_data						= \App\Models\Employee::id($value['id'])->personid($employ_id)->first();

				if(!$cals_data)
				{
					$errors->add('employee', 'Tidak ada Karyawan '.$value['name']);
				}

				if(!$errors->count())
				{
					$total 						= \App\Models\Work::personid($value['id'])->count();
					$parameter['employee_id']	= $value['id'];

					$queue 						= new \App\Models\Queue;
					$queue->fill([
							'process_name' 			=> 'hr:EmployeeDocuments',
							'process_option' 		=> 'delete',
							'parameter' 			=> json_encode($parameter),
							'total_process' 		=> $total,
							'task_per_process' 		=> 1,
							'process_number' 		=> 0,
							'total_task' 			=> $total,
							'message' 				=> 'Initial Commit',
						]);

					if(!$queue->save())
					{
						$errors->add('EmployeeDocument', $queue->getError());
					}
				}
			}
		}
		//End of validate employee EmployeeDocument

		if($errors->count())
		{
			DB::rollback();

			return new JSend('error', (array)Input::all(), $errors);
		}

		DB::commit();
		
		return new JSend('success', (array)$EmployeeDocument);
	}
}
