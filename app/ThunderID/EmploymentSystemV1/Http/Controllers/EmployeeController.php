<?php

namespace App\ThunderID\EmploymentSystemV1\Http\Controllers;

use App\Libraries\JSend;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EmployeeController extends Controller
{
	/**
	 * Display all employeers of an org
	 *
	 * @return Response
	 */
	public function index($org_id = null)
	{

		$result						= new \App\ThunderID\EmploymentSystemV1\Models\Employee;

		$result						= $result->organisationid($org_id);
		
		if(Input::has('search'))
		{
			$search					= Input::get('search');

			foreach ($search as $key => $value) 
			{
				switch (strtolower($key)) 
				{
					case 'name' :
						$result 		= $result->name($value);
						break;
					case 'email' :
						$result 		= $result->email($value);
						break;
					case 'nik' :
						$result 		= $result->nik($value);
						break;
					case 'workstatus' :
						$result 		= $result->workstatus($value);
						break;
					case 'workstart' :
						$result 		= $result->workstart($value);
						break;
					case 'workend' :
						$result 		= $result->workend($value);
						break;
					case 'department' :
						$result 		= $result->department($value);
						break;
					case 'position' :
						$result 		= $result->chartname($value);
						break;
					case 'branchname' :
						$result 		= $result->branchname($value);
						break;
					case 'grade' :
						$result 		= $result->grade($value);
						break;
					case 'currentgrade' :
						$result 		= $result->currentgrade($value);
						break;
					case 'maritalstatus' :
						$result 		= $result->maritalstatus($value);
						break;
					case 'currentmaritalstatus' :
						$result 		= $result->currentmaritalstatus($value);
						break;
					case 'documents' :
						$result 		= $result->with(['persondocuments']);
						break;
					case 'maritalstatuses' :
						$result 		= $result->with(['maritalstatuses']);
						break;
					case 'relatives' :
						$result 		= $result->with(['relatives', 'relatives.person']);
						break;
					case 'contacts' :
						$result 		= $result->with(['contacts']);
						break;
					case 'works' :
						$result 		= $result->with(['works', 'works.chart', 'works.chart.branch']);
						break;
					case 'contractworks' :
						$result 		= $result->with(['works', 'works.chart', 'works.chart.branch', 'works.contractworks', 'works.contractworks.contractelement']);
						break;
					default:
						# code...
						break;
				}
			}
		}

		if(Input::has('sort'))
		{
			$sort                 = Input::get('sort');

			foreach ($sort as $key => $value) 
			{
				if(!in_array($value, ['asc', 'desc']))
				{
					return new JSend('error', (array)Input::all(), $key.' harus bernilai asc atau desc.');
				}
				switch (strtolower($key)) 
				{
					case 'name':
						$result     = $result->orderby($key, $value);
						break;
					case 'workstart':
						$result     = $result->orderby('start', $value);
						break;
					case 'workend':
						$result     = $result->orderby('end', $value);
						break;
					default:
						# code...
						break;
				}
			}
		}

		$count						= count($result->get(['id']));

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

		$result						= $result->get()->toArray();

		return new JSend('success', (array)$result);
	}

	/**
	 * Display an employeer of an org
	 *
	 * @return Response
	 */
	public function detail($org_id = null, $id = null)
	{
		$startwork					= \App\ThunderID\EmploymentSystemV1\Models\Work::personid($id)->chartorganisationid($org_id)->orderby('start', 'desc')->first();
		$endwork					= \App\ThunderID\EmploymentSystemV1\Models\Work::personid($id)->chartorganisationid($org_id)->orderby('end', 'asc')->first();

		$result						= \App\ThunderID\EmploymentSystemV1\Models\Employee::id($id)->organisationid($org_id)->currentgrade(true)->currentmaritalstatus(true)->with(['persondocuments', 'maritalstatuses', 'relatives', 'relatives.person', 'contacts', 'works', 'works.contractworks', 'works.contractworks.contractelement'])->first();

		if($result)
		{
			$result 				= $result->toArray();
			
			if($endwork['end']->format('Y-m-d H:i:s')!='-0001-11-30 00:00:00')
			{
				$result['work_period'] 	= [$startwork['start']->format('Y-m-d H:i:s'), $endwork['end']->format('Y-m-d H:i:s')];
			}
			else
			{
				$result['work_period'] 	= [$startwork['start']->format('Y-m-d H:i:s'), Carbon::now()->format('Y-m-d H:i:s')];
			}

			return new JSend('success', (array)$result);
		}
		return new JSend('error', (array)Input::all(), 'ID Tidak Valid.');
	}

	/**
	 * Store an employeer
	 *
	 * 1. Save Employee
	 * 2. Save Contacts
	 * 3. Save Private Documents
	 * 4. Save Careers
	 * 5. Save Work Experience
	 * 6. Save Marital Status
	 * @return Response
	 */
	public function store($org_id = null)
	{
		if(!Input::has('employee'))
		{
			return new JSend('error', (array)Input::all(), 'Tidak ada data employee.');
		}

		$errors						= new MessageBag();

		DB::beginTransaction();

		//1. Validate employee Parameter
		$employee					= Input::get('employee');

		if(is_null($employee['id']))
		{
			$is_new					= true;
		}
		else
		{
			$is_new					= false;
		}

		$employee_rules				=   [
											'username'					=> 'max:255|unique:persons,username,'.(!is_null($employee['id']) ? $employee['id'] : ''),
											'name'						=> 'required|max:255',
											'prefix_title'				=> 'max:255',
											'suffix_title'				=> 'max:255',
											'place_of_birth'			=> 'max:255',
											'date_of_birth'				=> 'date_format:"Y-m-d H:i:s"',
											'gender'					=> 'in:female,male',
											'password'					=> 'max:255',
										];

		//1a. Get original data
		$employee_data				= \App\ThunderID\EmploymentSystemV1\Models\Employee::findornew($employee['id']);

		//1b. Validate Basic Employee Parameter
		$validator					= Validator::make($employee, $employee_rules);

		if (!$validator->passes())
		{
			$errors->add('Employee', $validator->errors());
		}
		else
		{
			//if validator passed, save Employee
			$employee_data			= $employee_data->fill($employee);

			if(!$employee_data->save())
			{
				$errors->add('Employee', $employee_data->getError());
			}
		}
		//End of validate Employee

		//2. Validate Employee contact Parameter
		if(!$errors->count() && isset($employee['contacts']) && is_array($employee['contacts']))
		{
			$contact_current_ids		= [];
			foreach ($employee['contacts'] as $key => $value) 
			{
				if(!$errors->count())
				{
					$contact_data		= \App\ThunderID\OrganisationManagementV1\Models\Contact::findornew($value['id']);

					
					$contact_rules		=	[
												'contactable_id'	=> 'exists:hrps_persons,id|'.($is_new ? '' : 'in:'.$employee_data['id']),
												'contactable_type'	=> (!$contact_data ? '' : 'in:'.get_class($employee_data)),
												'type'				=> 'required|max:255',
												'is_default'		=> 'boolean',
											];

					$validator			= Validator::make($value, $contact_rules);
					

					//if there was contact and validator false
					if (!$validator->passes())
					{
						$errors->add('contact', $validator->errors());
					}
					else
					{
						$value['contactable_id']	= $employee_data['id'];
						$value['contactable_type']	= get_class($employee_data);

						$contact_data				= $contact_data->fill($value);

						if(!$contact_data->save())
						{
							$errors->add('contact', $contact_data->getError());
						}
						else
						{
							$contact_current_ids[]	= $contact_data['id'];
						}
					}
				}
			}
			//if there was no error, check if there were things need to be delete
			if(!$errors->count())
			{
				$contacts							= \App\ThunderID\OrganisationManagementV1\Models\Contact::ContactableID($employee['id'])->ContactableType(get_class($employee_data))->get(['id'])->toArray();
				
				$contact_should_be_ids				= [];
				foreach ($contacts as $key => $value) 
				{
					$contact_should_be_ids[]		= $value['id'];
				}

				$difference_contact_ids				= array_diff($contact_should_be_ids, $contact_current_ids);

				if($difference_contact_ids)
				{
					foreach ($difference_contact_ids as $key => $value) 
					{
						$contact_data				= \App\ThunderID\OrganisationManagementV1\Models\Contact::find($value);

						if(!$contact_data->delete())
						{
							$errors->add('contact', $contact_data->getError());
						}
					}
				}
			}
		}
		//End of validate employee contact

		//3. Validate Employee privatedocument Parameter
		if(!$errors->count() && isset($employee['persondocuments']) && is_array($employee['persondocuments']))
		{
			$document_current_ids		= [];
			foreach ($employee['persondocuments'] as $key => $value) 
			{
				if(!$errors->count())
				{
					$document_data		= \App\ThunderID\PersonSystemV1\Models\PersonDocument::findornew($value['id']);

					
					$document_rules		=	[
												'person_id'			=> 'exists:hrps_persons,id|'.($is_new ? '' : 'in:'.$employee_data['id']),
												'documents'			=> 'required|json',
											];

					$validator			= Validator::make($value, $document_rules);
					

					//if there was contact and validator false
					if (!$validator->passes())
					{
						$errors->add('PersonDocument', $validator->errors());
					}
					else
					{
						$value['person_id']			= $employee_data['id'];

						$document_data				= $document_data->fill($value);

						if(!$document_data->save())
						{
							$errors->add('contact', $document_data->getError());
						}
						else
						{
							$document_current_ids[]	= $document_data['id'];
						}
					}
				}
			}
			//if there was no error, check if there were things need to be delete
			if(!$errors->count())
			{
				$persondocuments							= \App\ThunderID\PersonSystemV1\Models\PersonDocument::personid($employee['id'])->get(['id'])->toArray();
				
				$document_should_be_ids				= [];
				foreach ($persondocuments as $key => $value) 
				{
					$document_should_be_ids[]		= $value['id'];
				}

				$difference_contact_ids				= array_diff($document_should_be_ids, $document_current_ids);

				if($difference_contact_ids)
				{
					foreach ($difference_contact_ids as $key => $value) 
					{
						$document_data				= \App\ThunderID\PersonSystemV1\Models\PersonDocument::find($value);

						if(!$document_data->delete())
						{
							$errors->add('contact', $document_data->getError());
						}
					}
				}
			}
		}
		//End of validate employee privatedocument

		//4. Validate Employee Work Parameter
		if(!$errors->count() && isset($employee['works']) && is_array($employee['works']))
		{
			$work_current_ids			= [];
			foreach ($employee['works'] as $key => $value) 
			{
				if(!$errors->count())
				{
					$work_data		= \App\ThunderID\EmploymentSystemV1\Models\Work::findornew($value['id']);

					$work_rules		=   [
												'person_id'			=> 'exists:hrps_persons,id|'.($is_new ? '' : 'in:'.$employee_data['id']),
												'chart_id'			=> 'exists:hrom_charts,id',
												'grade'				=> 'required|numeric',
												'status'			=> 'required|in:contract,probation,internship,permanent,others,admin',
												'start'				=> 'required|date_format:"Y-m-d H:i:s"',
												// 'end'				=> 'date_format:"Y-m-d H:i:s"',
												'reason_end_job'	=> 'required_with:end',
											];

					$validator		= Validator::make($value, $work_rules);

					//if there was work and validator false
					if (!$validator->passes())
					{
						$errors->add('Work', $validator->errors());
					}
					else
					{
						$value['person_id']			= $employee_data['id'];

						$work_data				= $work_data->fill($value);

						if(!$work_data->save())
						{
							$errors->add('Work', $work_data->getError());
						}
						else
						{
							$grade_data			= \App\ThunderID\EmploymentSystemV1\Models\GradeLog::workid($value['id'])->orderby('updated_at', 'asc')->first();
							
							if(!$grade_data || $grade_data==$value['grade'])
							{
								$grade_data 	= new \App\ThunderID\EmploymentSystemV1\Models\GradeLog;

								$grade_data->fill(['grade' => $value['grade'], 'work_id' => $value['id']]);
		
								if(!$grade_data->save())
								{
									$errors->add('Work', $grade_data->getError());
								}
							}

							if(!$errors->count() && isset($value['contractworks']) && is_array($value['contractworks']))
							{
								$contract_current_ids		= [];
								foreach ($value['contractworks'] as $key => $value2) 
								{
									if(!$errors->count())
									{
										$contract_data		= \App\ThunderID\EmploymentSystemV1\Models\ContractWork::findornew($value2['id']);

										
										$contract_rules		=	[
																	'work_id'				=> 'exists:hres_contracts_works,id|'.($is_new ? '' : 'in:'.$work_data['id']),
																	'contract_element_id'	=> 'exists:hres_contract_elements,id|'.(($contract_data!=null) ? '' : 'in:'.$contract_data['contract_element_id']),
																	'value'					=> 'required|max:255',
																];

										$validator			= Validator::make($value2, $contract_rules);
										

										//if there was contract and validator false
										if (!$validator->passes())
										{
											$errors->add('contract', $validator->errors());
										}
										else
										{
											$value2['work_id']			= $work_data['id'];

											$contract_data				= $contract_data->fill($value2);

											if(!$contract_data->save())
											{
												$errors->add('contract', $contract_data->getError());
											}
											else
											{
												$contract_current_ids[]	= $contract_data['id'];
											}
										}
									}
								}
								//if there was no error, check if there were things need to be delete
								if(!$errors->count())
								{
									$contractworks							= \App\ThunderID\EmploymentSystemV1\Models\ContractWork::workid($value['id'])->get(['id'])->toArray();
									
									$contract_should_be_ids				= [];
									foreach ($contractworks as $key => $value2) 
									{
										$contract_should_be_ids[]		= $value2['id'];
									}

									$difference_contract_ids				= array_diff($contract_should_be_ids, $contract_current_ids);

									if($difference_contract_ids)
									{
										foreach ($difference_contract_ids as $key => $value2) 
										{
											$contract_data				= \App\ThunderID\EmploymentSystemV1\Models\ContractWork::find($value2);

											if(!$contract_data->delete())
											{
												$errors->add('contract', $contract_data->getError());
											}
										}
									}
								}
							}
							$work_current_ids[]	= $work_data['id'];
						}
					}
				}
			}

			//if there was no error, check if there were things need to be delete
			if(!$errors->count())
			{
				$works							= \App\ThunderID\EmploymentSystemV1\Models\Work::personid($employee['id'])->get(['id'])->toArray();
				
				$work_should_be_ids				= [];
				foreach ($works as $key => $value) 
				{
					$work_should_be_ids[]			= $value['id'];
				}

				$difference_work_ids				= array_diff($work_should_be_ids, $work_current_ids);

				if($difference_work_ids)
				{
					foreach ($difference_work_ids as $key => $value) 
					{
						$work_data				= \App\ThunderID\EmploymentSystemV1\Models\Work::find($value);

						if(!$work_data->delete())
						{
							$errors->add('Work', $work_data->getError());
						}
					}
				}
			}
		}
		//End of validate employee career

		//5. Validate Employee relatives Parameter
		if(!$errors->count() && isset($employee['relatives']) && is_array($employee['relatives']))
		{
			$relative_current_ids			= [];

			foreach ($employee['relatives'] as $key => $value) 
			{
				if(!$errors->count())
				{
					$relative_data		= \App\ThunderID\PersonSystemV1\Models\Relative::findornew($value['id']);

					$relative_rules		=	[
												'person_id'					=> 'exists:hrps_persons,id|'.($is_new ? '' : 'in:'.$employee_data['id']),
												'relative_id'				=> 'exists:hrps_persons,id|'.(($relative_data['relative_id']==null) ? '' : 'in:'.$relative_data['relative_id']),
												'relationship'				=> 'required|max:255',
												'person.name'				=> 'required|max:255',
												'person.prefix_title'		=> 'max:255',
												'person.suffix_title'		=> 'max:255',
												'person.place_of_birth'		=> 'max:255',
												'person.date_of_birth'		=> 'date_format:"Y-m-d H:i:s"',
												'person.gender'				=> 'in:female,male',
											];

					$validator					= Validator::make($value, $relative_rules);

					//if there was workexperience and validator false
					if (!$validator->passes())
					{
						$errors->add('workexperience', $validator->errors());
					}
					else
					{
						$employee_relative_data		= \App\ThunderID\EmploymentSystemV1\Models\Employee::findornew($relative_data['person']['id']);
						$validator					= Validator::make($employee, $employee_rules);

						if (!$validator->passes())
						{
							$errors->add('Employee', $validator->errors());
						}
						else
						{
							//if validator passed, save Employee
							$employee_relative_data	= $employee_relative_data->fill($value['person']);

							if(!$employee_relative_data->save())
							{
								$errors->add('Employee', $employee_relative_data->getError());
							}
						}

						if(!$errors->count())
						{
							$value['person_id']			= $employee_data['id'];
							$relative_data				= $relative_data->fill($value);

							if(!$relative_data->save())
							{
								$errors->add('workexperience', $relative_data->getError());
							}
							else
							{
								$relative_current_ids[]	= $relative_data['id'];
							}
						}

					}
				}
			}
			//if there was no error, check if there were things need to be delete
			if(!$errors->count())
			{
				$relatives								= \App\ThunderID\PersonSystemV1\Models\Relative::personid($employee['id'])->get(['id'])->toArray();
				
				$relative_should_be_ids				= [];
				foreach ($relatives as $key => $value) 
				{
					$relative_should_be_ids[]			= $value['id'];
				}

				$difference_workexperience_ids				= array_diff($relative_should_be_ids, $relative_current_ids);

				if($difference_workexperience_ids)
				{
					foreach ($difference_workexperience_ids as $key => $value) 
					{
						$relative_data				= \App\ThunderID\PersonSystemV1\Models\Relative::find($value);

						if(!$relative_data->delete())
						{
							$errors->add('workexperience', $relative_data->getError());
						}
					}
				}
			}
		}
		//End of validate employee relatives

		//6. Validate Employee ms Parameter
		if(!$errors->count() && isset($employee['maritalstatuses']) && is_array($employee['maritalstatuses']))
		{
			$ms_current_ids			= [];
			foreach ($employee['maritalstatuses'] as $key => $value) 
			{
				if(!$errors->count())
				{
					$ms_data		= \App\ThunderID\PersonSystemV1\Models\MaritalStatus::findornew($value['id']);

					$ms_rules   	=   [
											'person_id'			=> 'exists:hrps_persons,id|'.($is_new ? '' : 'in:'.$employee_data['id']),
											'status'			=> 'required|max:255',
											'ondate'			=> 'required|date_format:"Y-m-d H:i:s"',
										];

					$validator		= Validator::make($value, $ms_rules);

					//if there was ms and validator false
					if (!$validator->passes())
					{
						$errors->add('MaritalStatus', $validator->errors());
					}
					else
					{
						$value['person_id']		= $employee_data['id'];

						$ms_data				= $ms_data->fill($value);

						if(!$ms_data->save())
						{
							$errors->add('ms', $ms_data->getError());
						}
						else
						{
							$ms_current_ids[]	= $ms_data['id'];
						}
					}
				}
			}
			//if there was no error, check if there were things need to be delete
			if(!$errors->count())
			{
				$mss							= \App\ThunderID\PersonSystemV1\Models\MaritalStatus::personid($employee['id'])->get(['id'])->toArray();
				
				$ms_should_be_ids				= [];
				foreach ($mss as $key => $value) 
				{
					$ms_should_be_ids[]			= $value['id'];
				}

				$difference_ms_ids				= array_diff($ms_should_be_ids, $ms_current_ids);

				if($difference_ms_ids)
				{
					foreach ($difference_ms_ids as $key => $value) 
					{
						$ms_data				= \App\ThunderID\PersonSystemV1\Models\MaritalStatus::find($value);

						if(!$ms_data->delete())
						{
							$errors->add('MaritalStatus', $ms_data->getError());
						}
					}
				}
			}
		}
		//End of validate employee MaritalStatus

		if($errors->count())
		{
			DB::rollback();

			return new JSend('error', (array)Input::all(), $errors);
		}

		DB::commit();
		
		$final_employee			= \App\ThunderID\EmploymentSystemV1\Models\Employee::id($employee_data['id'])->organisationid($org_id)->currentgrade(true)->currentmaritalstatus(true)->with(['persondocuments', 'maritalstatuses', 'relatives', 'relatives.person', 'contacts', 'works', 'works.contractworks', 'works.contractworks.contractelement'])->first()->toArray();

		return new JSend('success', (array)$final_employee);
	}

	/**
	 * Delete an employee
	 *
	 * @return Response
	 */
	public function delete($org_id = null, $id = null)
	{
		//
		$employee				= \App\ThunderID\EmploymentSystemV1\Models\Employee::id($id)->organisationid($org_id)->currentgrade(true)->currentmaritalstatus(true)->with(['persondocuments', 'maritalstatuses', 'relatives', 'relatives.person', 'contacts', 'works', 'works.contractworks', 'works.contractworks.contractelement'])->first();

		if(!$employee)
		{
			return new JSend('error', (array)Input::all(), 'Karyawan tidak ditemukan.');
		}

		$result					= $employee->toArray();

		if($employee->delete())
		{
			return new JSend('success', (array)$result);
		}

		return new JSend('error', (array)$result, $employee->getError());
	}

	/**
	 * Display distinct grade
	 *
	 * @return Response
	 */
	public function grades($org_id = null)
	{
		$result						= \App\ThunderID\EmploymentSystemV1\Models\GradeLog::wherehas('work', function($q)use($org_id){$q->chartorganisationid($org_id);})->groupby('grade')->distinct()->get(['grade']);

		return new JSend('success', (array)$result->toArray());
	}

	/**
	 * Display distinct maritalstatus
	 *
	 * @return Response
	 */
	public function MaritalStatuses($org_id = null)
	{
		$result						= \App\ThunderID\PersonSystemV1\Models\MaritalStatus::wherehas('person.works', function($q)use($org_id){$q->chartorganisationid($org_id);})->groupby('status')->distinct()->get(['status']);

		return new JSend('success', (array)$result->toArray());
	}
}
