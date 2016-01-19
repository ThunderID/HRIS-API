<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
	/**
	 * Display all employeers of an org
	 *
	 * @return Response
	 */
	public function index($org_id = null)
	{
		$result						= new \App\Models\Employee;

		$result						= $result->organisationid($org_id);
		
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

		$result						= $result->with(['privatedocuments', 'privatedocuments.document', 'privatedocuments.documentdetails', 'privatedocuments.documentdetails.template'])->get()->toArray();

		return new JSend('success', (array)$result);
	}

	/**
	 * Display an employeer of an org
	 *
	 * @return Response
	 */
	public function detail($org_id = null, $id = null)
	{
		$result						= \App\Models\Employee::id($id)->organisationid($org_id)->with(['privatedocuments', 'privatedocuments.document', 'privatedocuments.documentdetails', 'privatedocuments.documentdetails.template', 'careers', 'workexperiences', 'maritalstatuses', 'contacts'])->first();

		if($result)
		{
			return new JSend('success', (array)$result->toArray());
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
											'organisation_id'			=> 'exists:organisations,id',
											'uniqid'					=> 'required|max:255|unique:persons,uniqid,'.(!is_null($employee['id']) ? $employee['id'] : ''),
											'username'					=> 'max:255|unique:persons,username,'.(!is_null($employee['id']) ? $employee['id'] : ''),
											'name'						=> 'required|max:255',
											'prefix_title'				=> 'max:255',
											'suffix_title'				=> 'max:255',
											'place_of_birth'			=> 'required|max:255',
											'date_of_birth'				=> 'required|date_format:"Y-m-d H:i:s"',
											'gender'					=> 'required|in:female,male',
											'password'					=> 'max:255',
											'last_password_updated_at'	=> 'date_format:"Y-m-d H:i:s"|before:tomorrow',
										];

		//1a. Get original data
		$employee_data				= \App\Models\Employee::findornew($employee['id']);

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
					$contact_data		= \App\Models\PersonContact::find($value['id']);

					if($contact_data)
					{
						$contact_rules	=	[
												'person_id'		=> 'exists:persons,id|'.($is_new ? '' : 'in:'.$employee_data['id']),
												'item'			=> 'required|max:255',
												'value'			=> 'required|max:255',
												'is_default'	=> 'required|max:255',
											];

						$validator		= Validator::make($contact_data['attributes'], $contact_rules);
					}
					else
					{
						$contact_rules   =   [
												'person_id'		=> 'exists:persons,id|'.($is_new ? '' : 'in:'.$employee_data['id']),
												'item'			=> 'required|max:255',
												'value'			=> 'required|max:255',
												'is_default'	=> 'required|max:255',
											];

						$validator		= Validator::make($value, $contact_rules);
					}

					//if there was contact and validator false
					if ($contact_data && !$validator->passes())
					{
						if($value['person_id']!=$employee['id'])
						{
							$errors->add('contact', 'Dokumen Karyawan Tidak Valid.');
						}
						elseif($is_new)
						{
							$errors->add('contact', 'Dokumen Karyawan Tidak Valid.');
						}
						else
						{
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
					//if there was contact and validator false
					elseif (!$contact_data && !$validator->passes())
					{
						$errors->add('contact', $validator->errors());
					}
					elseif($contact_data && $validator->passes())
					{
						$contact_current_ids[]		= $contact_data['id'];
					}
					else
					{
						$value['person_id']			= $employee_data['id'];
						$value['person_type']		= 'App\Models\Employee';

						$contact_data				= new \App\Models\PersonContact;

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
				$contacts							= \App\Models\PersonContact::personid($employee['id'])->get()->toArray();
				
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
						$contact_data				= \App\Models\PersonContact::find($value);

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
		if(!$errors->count() && isset($employee['privatedocuments']) && is_array($employee['privatedocuments']))
		{
			$pd_current_ids			= [];
			foreach ($employee['privatedocuments'] as $key => $value) 
			{
				if(!$errors->count())
				{
					$pd_data		= \App\Models\PrivateDocument::find($value['id']);

					if($pd_data)
					{
						$pd_rules	=   [
											'document_id'		=> 'exists:tmp_documents,id|'.($is_new ? '' : 'in:'.$employee_data['id']),
											'person_id'			=> 'exists:persons,id|'.($is_new ? '' : 'in:'.$employee_data['id']),
										];

						$validator	= Validator::make($pd_data['attributes'], $pd_rules);
					}
					else
					{
						$pd_rules	=	[
											'document_id'		=> 'exists:tmp_documents,id|'.($is_new ? '' : 'in:'.$employee_data['id']),
											'person_id'			=> 'exists:persons,id|'.($is_new ? '' : 'in:'.$employee_data['id']),
										];

						$validator	= Validator::make($value, $pd_rules);
					}

					//if there was privatedocument and validator false
					if ($pd_data && !$validator->passes())
					{
						if($value['person_id']!=$employee['id'])
						{
							$errors->add('privatedocument', 'Dokumen Karyawan Tidak Valid.');
						}
						elseif($is_new)
						{
							$errors->add('privatedocument', 'Dokumen Karyawan Tidak Valid.');
						}
						else
						{
							$pd_data				= $pd_data->fill($value);

							if(!$pd_data->save())
							{
								$errors->add('privatedocument', $pd_data->getError());
							}
							else
							{
								$pd_current_ids[]	= $pd_data['id'];
							}
						}
					}
					//if there was privatedocument and validator false
					elseif (!$pd_data && !$validator->passes())
					{
						$errors->add('privatedocument', $validator->errors());
					}
					elseif($pd_data && $validator->passes())
					{
						$pd_current_ids[]			= $pd_data['id'];
					}
					else
					{
						$value['person_id']			= $employee_data['id'];

						$pd_data					= new \App\Models\PrivateDocument;

						$pd_data					= $pd_data->fill($value);

						if(!$pd_data->save())
						{
							$errors->add('privatedocument', $pd_data->getError());
						}
						else
						{
							$pd_current_ids[]		= $pd_data['id'];
						}
					}

					//check template
					$dd_current_ids			= [];
					foreach ($value['documentdetails'] as $key2 => $value2) 
					{
						if(!$errors->count())
						{
							$dd_data		= \App\Models\DocumentDetail::find($value2['id']);

							if($dd_data)
							{
								$dd_rules	=	[
													'person_document_id'	=> 'exists:persons_documents,id|'.($is_new ? '' : 'in:'.$employee_data['id']),
													'template_id'			=> 'exists:tmp_templates,id',
												];

								$validator		= Validator::make($dd_data['attributes'], $dd_rules);
							}
							else
							{
								$dd_rules   =   [
													'person_document_id'	=> 'exists:persons_documents,id|'.($is_new ? '' : 'in:'.$employee_data['id']),
													'template_id'			=> 'exists:tmp_templates,id',
												];

								$validator		= Validator::make($value2, $dd_rules);
							}

							$template_data 		= \App\Models\Template::find($value2['template_id']);

							if($template_data)
							{
								//if there was dd and validator false
								if ($dd_data && !$validator->passes())
								{
									if($value2['person_document_id']!=$pd_data['id'])
									{
										$errors->add('dd', 'Dokumen Karyawan Tidak Valid.');
									}
									elseif($is_new)
									{
										$errors->add('dd', 'Dokumen Karyawan Tidak Valid.');
									}
									else
									{
										$param[($template_data['type']=='date' ? 'on' : strtolower($template_data['type']))] = $value2[($template_data['type']=='date' ? 'on' : strtolower($template_data['type']))];
										$param['template_id']	= $template_data['id'];
										$param['person_document_id']	= $pd_data['id'];

										$dd_data				= $dd_data->fill($param);

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
								//if there was dd and validator false
								elseif (!$dd_data && !$validator->passes())
								{
									$errors->add('dd', $validator->errors());
								}
								elseif($dd_data && $validator->passes())
								{
									$dd_current_ids[]				= $dd_data['id'];
								}
								else
								{
									$dd_data						= new \App\Models\DocumentDetail;
									$param[($template_data['type']=='date' ? 'on' : strtolower($template_data['type']))] = $value2[($template_data['type']=='date' ? 'on' : strtolower($template_data['type']))];
									$param['template_id']			= $template_data['id'];
									$param['person_document_id']	= $pd_data['id'];

									$dd_data				= $dd_data->fill($param);

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
						$dds							= \App\Models\DocumentDetail::persondocumentid($pd_data['id'])->get()->toArray();
						
						$dd_should_be_ids				= [];
						foreach ($dds as $key2 => $value2) 
						{
							$dd_should_be_ids[]			= $value2['id'];
						}

						$difference_dd_ids				= array_diff($dd_should_be_ids, $dd_current_ids);

						if($difference_dd_ids)
						{
							foreach ($difference_dd_ids as $key2 => $value2) 
							{
								$dd_data				= \App\Models\DocumentDetail::find($value2);

								if(!$dd_data->delete())
								{
									$errors->add('dd', $dd_data->getError());
								}
							}
						}
					}
				}
			}

			//if there was no error, check if there were things need to be delete
			if(!$errors->count())
			{
				$privatedocuments					= \App\Models\PrivateDocument::personid($employee['id'])->get()->toArray();
				
				$pd_should_be_ids					= [];
				foreach ($privatedocuments as $key => $value) 
				{
					$pd_should_be_ids[]				= $value['id'];
				}

				$difference_privatedocument_ids		= array_diff($pd_should_be_ids, $pd_current_ids);

				if($difference_privatedocument_ids)
				{
					foreach ($difference_privatedocument_ids as $key => $value) 
					{
						$pd_data					= \App\Models\PrivateDocument::find($value);

						if(!$pd_data->delete())
						{
							$errors->add('privatedocument', $contact_data->getError());
						}
					}
				}
			}
		}
		//End of validate employee privatedocument

		//4. Validate Employee career Parameter
		if(!$errors->count() && isset($employee['careers']) && is_array($employee['careers']))
		{
			$career_current_ids			= [];
			foreach ($employee['careers'] as $key => $value) 
			{
				if(!$errors->count())
				{
					$career_data		= \App\Models\Career::find($value['id']);

					if($career_data)
					{
						$career_rules	=	[
												'person_id'			=> 'exists:persons,id|'.($is_new ? '' : 'in:'.$employee_data['id']),
												'calendar_id'		=> 'exists:tmp_calendars,id',
												'chart_id'			=> 'exists:charts,id',
												'grade'				=> 'numeric',
												'status'			=> 'required|in:contract,probation,internship,permanent,others,admin',
												'start'				=> 'required|date_format:"Y-m-d H:i:s"',
												'end'				=> 'date_format:"Y-m-d H:i:s"',
												'reason_end_job'	=> 'required_with:end',
												'is_absence'		=> 'boolean',
											];

						$validator		= Validator::make($career_data['attributes'], $career_rules);
					}
					else
					{
						$career_rules   =   [
												'person_id'			=> 'exists:persons,id|'.($is_new ? '' : 'in:'.$employee_data['id']),
												'calendar_id'		=> 'exists:tmp_calendars,id',
												'chart_id'			=> 'exists:charts,id',
												'grade'				=> 'numeric',
												'status'			=> 'required|in:contract,probation,internship,permanent,others,admin',
												'start'				=> 'required|date_format:"Y-m-d H:i:s"',
												'end'				=> 'date_format:"Y-m-d H:i:s"',
												'reason_end_job'	=> 'required_with:end',
												'is_absence'		=> 'boolean',
											];

						$validator		= Validator::make($value, $career_rules);
					}

					//if there was career and validator false
					if ($career_data && !$validator->passes())
					{
						if($value['person_id']!=$employee['id'])
						{
							$errors->add('career', 'Pekerjaan Tidak Valid.');
						}
						elseif($is_new)
						{
							$errors->add('career', 'Pekerjaan Tidak Valid.');
						}
						else
						{
							$career_data				= $career_data->fill($value);

							if(!$career_data->save())
							{
								$errors->add('career', $career_data->getError());
							}
							else
							{
								$career_current_ids[]	= $career_data['id'];
							}
						}
					}
					//if there was career and validator false
					elseif (!$career_data && !$validator->passes())
					{
						$errors->add('career', $validator->errors());
					}
					elseif($career_data && $validator->passes())
					{
						$career_current_ids[]		= $career_data['id'];
					}
					else
					{
						$career_data				= new \App\Models\Career;

						$career_data				= $career_data->fill($value);

						if(!$career_data->save())
						{
							$errors->add('career', $career_data->getError());
						}
						else
						{
							$career_current_ids[]	= $career_data['id'];
						}
					}
				}
			}
			//if there was no error, check if there were things need to be delete
			if(!$errors->count())
			{
				$careers							= \App\Models\Career::personid($employee['id'])->get()->toArray();
				
				$career_should_be_ids				= [];
				foreach ($careers as $key => $value) 
				{
					$career_should_be_ids[]			= $value['id'];
				}

				$difference_career_ids				= array_diff($career_should_be_ids, $career_current_ids);

				if($difference_career_ids)
				{
					foreach ($difference_career_ids as $key => $value) 
					{
						$career_data				= \App\Models\Career::find($value);

						if(!$career_data->delete())
						{
							$errors->add('career', $career_data->getError());
						}
					}
				}
			}
		}
		//End of validate employee career

		//5. Validate Employee workexperience Parameter
		if(!$errors->count() && isset($employee['workexperiences']) && is_array($employee['workexperiences']))
		{
			$workexperience_current_ids			= [];
			foreach ($employee['workexperiences'] as $key => $value) 
			{
				if(!$errors->count())
				{
					$workexperience_data		= \App\Models\WorkExperience::find($value['id']);

					if($workexperience_data)
					{
						$workexperience_rules	=	[
												'person_id'			=> 'exists:persons,id|'.($is_new ? '' : 'in:'.$employee_data['id']),
												'position'			=> 'required|max:255',
												'organisation'		=> 'required|max:255',
												'grade'				=> 'numeric',
												'status'			=> 'required|in:previous',
												'start'				=> 'required|date_format:"Y-m-d H:i:s"',
												'end'				=> 'required|date_format:"Y-m-d H:i:s"',
												'reason_end_job'	=> 'required_with:end',
												'is_absence'		=> 'boolean',
											];

						$validator		= Validator::make($workexperience_data['attributes'], $workexperience_rules);
					}
					else
					{
						$workexperience_rules   =   [
												'person_id'			=> 'exists:persons,id|'.($is_new ? '' : 'in:'.$employee_data['id']),
												'position'			=> 'required|max:255',
												'organisation'		=> 'required|max:255',
												'grade'				=> 'numeric',
												'status'			=> 'required|in:previous',
												'start'				=> 'required|date_format:"Y-m-d H:i:s"',
												'end'				=> 'required|date_format:"Y-m-d H:i:s"',
												'reason_end_job'	=> 'required_with:end',
												'is_absence'		=> 'boolean',
											];

						$validator		= Validator::make($value, $workexperience_rules);
					}

					//if there was workexperience and validator false
					if ($workexperience_data && !$validator->passes())
					{
						if($value['person_id']!=$employee['id'])
						{
							$errors->add('workexperience', 'Pekerjaan Tidak Valid.');
						}
						elseif($is_new)
						{
							$errors->add('workexperience', 'Pekerjaan Tidak Valid.');
						}
						else
						{
							$workexperience_data				= $workexperience_data->fill($value);

							if(!$workexperience_data->save())
							{
								$errors->add('workexperience', $workexperience_data->getError());
							}
							else
							{
								$workexperience_current_ids[]	= $workexperience_data['id'];
							}
						}
					}
					//if there was workexperience and validator false
					elseif (!$workexperience_data && !$validator->passes())
					{
						$errors->add('workexperience', $validator->errors());
					}
					elseif($workexperience_data && $validator->passes())
					{
						$workexperience_current_ids[]		= $workexperience_data['id'];
					}
					else
					{
						$workexperience_data				= new \App\Models\WorkExperience;

						$workexperience_data				= $workexperience_data->fill($value);

						if(!$workexperience_data->save())
						{
							$errors->add('workexperience', $workexperience_data->getError());
						}
						else
						{
							$workexperience_current_ids[]	= $workexperience_data['id'];
						}
					}
				}
			}
			//if there was no error, check if there were things need to be delete
			if(!$errors->count())
			{
				$workexperiences							= \App\Models\WorkExperience::personid($employee['id'])->get()->toArray();
				
				$workexperience_should_be_ids				= [];
				foreach ($workexperiences as $key => $value) 
				{
					$workexperience_should_be_ids[]			= $value['id'];
				}

				$difference_workexperience_ids				= array_diff($workexperience_should_be_ids, $workexperience_current_ids);

				if($difference_workexperience_ids)
				{
					foreach ($difference_workexperience_ids as $key => $value) 
					{
						$workexperience_data				= \App\Models\WorkExperience::find($value);

						if(!$workexperience_data->delete())
						{
							$errors->add('workexperience', $workexperience_data->getError());
						}
					}
				}
			}
		}
		//End of validate employee workexperience

		//6. Validate Employee ms Parameter
		if(!$errors->count() && isset($employee['maritalstatuses']) && is_array($employee['maritalstatuses']))
		{
			$ms_current_ids			= [];
			foreach ($employee['maritalstatuses'] as $key => $value) 
			{
				if(!$errors->count())
				{
					$ms_data		= \App\Models\MaritalStatus::find($value['id']);

					if($ms_data)
					{
						$ms_rules	=	[
												'person_id'			=> 'exists:persons,id|'.($is_new ? '' : 'in:'.$employee_data['id']),
												'status'			=> 'required|max:255',
												'on'				=> 'required|date_format:"Y-m-d H:i:s"',
											];

						$validator		= Validator::make($ms_data['attributes'], $ms_rules);
					}
					else
					{
						$ms_rules   =   [
												'person_id'			=> 'exists:persons,id|'.($is_new ? '' : 'in:'.$employee_data['id']),
												'status'			=> 'required|max:255',
												'on'				=> 'required|date_format:"Y-m-d H:i:s"',
											];

						$validator		= Validator::make($value, $ms_rules);
					}

					//if there was ms and validator false
					if ($ms_data && !$validator->passes())
					{
						if($value['person_id']!=$employee['id'])
						{
							$errors->add('ms', 'Pekerjaan Tidak Valid.');
						}
						elseif($is_new)
						{
							$errors->add('ms', 'Pekerjaan Tidak Valid.');
						}
						else
						{
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
					//if there was ms and validator false
					elseif (!$ms_data && !$validator->passes())
					{
						$errors->add('ms', $validator->errors());
					}
					elseif($ms_data && $validator->passes())
					{
						$ms_current_ids[]		= $ms_data['id'];
					}
					else
					{
						$ms_data				= new \App\Models\MaritalStatus;

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
				$mss							= \App\Models\MaritalStatus::personid($employee['id'])->get()->toArray();
				
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
						$ms_data				= \App\Models\MaritalStatus::find($value);

						if(!$ms_data->delete())
						{
							$errors->add('ms', $ms_data->getError());
						}
					}
				}
			}
		}
		//End of validate employee ms

		if($errors->count())
		{
			DB::rollback();

			return new JSend('error', (array)Input::all(), $errors);
		}

		DB::commit();
		
		$final_employee			= \App\Models\Employee::id($id)->organisationid($org_id)->with(['privatedocuments', 'privatedocuments.document', 'privatedocuments.documentdetails', 'privatedocuments.documentdetails.template', 'careers', 'workexperiences', 'maritalstatuses', 'contacts'])->first()->toArray();

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
		$employee				= \App\Models\Employee::id($id)->organisationid($org_id)->with(['privatedocuments', 'privatedocuments.document', 'privatedocuments.documentdetails', 'privatedocuments.documentdetails.template', 'careers', 'workexperiences', 'maritalstatuses', 'contacts'])->first();

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
}
