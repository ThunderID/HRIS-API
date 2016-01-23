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
		// if(!Input::has('employmentdocument'))
		// {
		// 	return new JSend('error', (array)Input::all(), 'Tidak ada data employment document.');
		// }

		$errors							= new MessageBag();

		DB::beginTransaction();

		//1. store EmployeeDocument of employee to queue
		// $employdoc						= Input::get('employmentdocument');
		$id = 4089;
		$employdoc						= \App\Models\EmploymentDocument::id(4089)->personid($employ_id)->with(['document', 'documentdetails', 'documentdetails.template'])->first()->toArray();

		if(is_null($employdoc['id']))
		{
			$is_new						= true;
		}
		else
		{
			$is_new						= false;
		}

		$ed_data						= \App\Models\EmploymentDocument::findornew($employdoc['id']);

		$ed_rules						=	[
												'document_id'		=> 'exists:tmp_documents,id|'.($is_new ? '' : 'in:'.$ed_data['document_id']),
												'person_id'			=> 'exists:persons,id|'.($is_new ? '' : 'in:'.$employ_id),
											];

		$validator						= Validator::make($employdoc, $ed_rules);

		//if there was employmentdocument and validator false
		if(!$validator->passes())
		{
			$errors->add('employmentdocument', $validator->errors());
		}
		else
		{
			$value['person_id']			= $employ_id;

			$ed_data					= $ed_data->fill($employdoc);

			if(!$ed_data->save())
			{
				$errors->add('employmentdocument', $ed_data->getError());
			}
		}

		if(!$errors->count() && isset($employdoc['documentdetails']) && is_array($employdoc['documentdetails']))
		{
			//check template
			$dd_current_ids			= [];
			foreach ($employdoc['documentdetails'] as $key => $value) 
			{
				if(!$errors->count())
				{
					$param 			= [];
					$dd_data		= \App\Models\DocumentDetail::findornew($value['id']);

					$dd_rules		=   [
											'person_document_id'	=> 'exists:persons_documents,id|'.($is_new ? '' : 'in:'.$ed_data['id']),
											'template_id'			=> 'exists:tmp_templates,id',
										];

					$validator		= Validator::make($value, $dd_rules);

					$template_data	= \App\Models\Template::find($value['template_id']);

					if($template_data)
					{
						//if there was dd and validator false
						if (!$validator->passes())
						{
							$errors->add('dd', $validator->errors());
						}
						else
						{
							$param[($template_data['type']=='date' ? 'on' : strtolower($template_data['type']))] = $value[($template_data['type']=='date' ? 'on' : strtolower($template_data['type']))];
							
							$param['template_id']			= $template_data['id'];
							$param['person_document_id']	= $ed_data['id'];

							if(isset($param['on']))
							{
								$param['on']				= date('Y-m-d H:i:s', strtotime($param['on']));
							}

							$dd_data						= $dd_data->fill($param);

							if(!$dd_data->save())
							{
								$errors->add('dd', $dd_data->getError());
							}
							else
							{
								$dd_current_ids[]	= $dd_data['id'];
							}
						}
					}
				}
			}
					
			//if there was no error, check if there were things need to be delete
			if(!$errors->count())
			{
				$dds							= \App\Models\DocumentDetail::persondocumentid($ed_data['id'])->get(['id'])->toArray();
				
				$dd_should_be_ids				= [];
				foreach ($dds as $key2 => $value) 
				{
					$dd_should_be_ids[]			= $value['id'];
				}

				$difference_dd_ids				= array_diff($dd_should_be_ids, $dd_current_ids);

				if($difference_dd_ids)
				{
					foreach ($difference_dd_ids as $key2 => $value) 
					{
						$dd_data				= \App\Models\DocumentDetail::find($value);

						if(!$dd_data->delete())
						{
							$errors->add('dd', $dd_data->getError());
						}
					}
				}
			}
		}
		//End of validate EmployeeDocument

		if($errors->count())
		{
			DB::rollback();

			return new JSend('error', (array)Input::all(), $errors);
		}

		DB::commit();
		
		$final_employdoc					= \App\Models\EmploymentDocument::id($ed_data['id'])->personid($employ_id)->with(['document', 'documentdetails', 'documentdetails.template'])->first()->toArray();

		return new JSend('success', (array)$final_employdoc);
	}


	/**
	 * Delete a EmployeeDocument (throw to queue)
	 *
	 * @return Response
	 */
	public function delete($org_id = null, $employ_id = null, $id = null)
	{
		//
		$employdoc					= \App\Models\EmploymentDocument::id($id)->personid($employ_id)->with(['document', 'documentdetails', 'documentdetails.template'])->first();

		if(!$employdoc)
		{
			return new JSend('error', (array)Input::all(), 'Jadwal tidak ditemukan.');
		}

		$errors						= new MessageBag();

		DB::beginTransaction();

		//1. store EmployeeDocument of employee to queue
		$employdoc					= $employdoc->toArray();

		if(is_null($employdoc['id']))
		{
			$is_new					= true;
		}
		else
		{
			$is_new					= false;
		}

		$employdoc_rules				=	[
											'employee_id'					=> 'required|exists:tmp_employees,id',
											'name'							=> 'required|max:255',
											'status'						=> 'required|in:DN,CB,UL,HB,L',
											'on'							=> 'required|date_format:"Y-m-d H:i:s"',
											'start'							=> 'required|date_format:"H:i:s"',
											'end'							=> 'required|date_format:"H:i:s"',
										];

		//1a. Validate Basic EmployeeDocument Parameter
		$parameter 					= $employdoc;
		unset($parameter['employee']);

		$validator					= Validator::make($parameter, $employdoc_rules);

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
		if(!$errors->count() && isset($employdoc['employee']['employees']) && is_array($employdoc['employee']['employees']))
		{
			foreach ($employdoc['employee']['employees'] as $key => $value) 
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
		
		return new JSend('success', (array)$employdoc);
	}
}
