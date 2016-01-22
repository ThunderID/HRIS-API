<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
	/**
	 * Display a branch of an org
	 *
	 * @return Response
	 */
	public function detail($org_id = null, $id = null)
	{
		$result						= \App\Models\Branch::id($id)->organisationid($org_id)->with(['charts', 'charts.follows', 'charts.follows.calendar', 'charts.chartworkleaves', 'charts.chartworkleaves.workleave', 'contacts', 'apis', 'fingerprint'])->first();

		if($result)
		{
			return new JSend('success', (array)$result->toArray());
		}

		return new JSend('error', (array)Input::all(), 'ID Tidak Valid.');
	}

	/**
	 * store a branch of an org
	 *
	 * 1. Save Branch
	 * 2. Save Charts
	 * 3. Save Contacs
	 * 4. Save Apis
	 * 5. Save finger print
	 * @return Response
	 */
	public function store($org_id = null)
	{
		if(!Input::has('branch'))
		{
			return new JSend('error', (array)Input::all(), 'Tidak ada data branch.');
		}

		$errors						= new MessageBag();

		DB::beginTransaction();

		//1. Validate branch Parameter
		$branch				= Input::get('branch');

		if(is_null($branch['id']))
		{
			$is_new					= true;
		}
		else
		{
			$is_new					= false;
		}

		$branch_rules				=	[
											'name'			=> 'required|max:255',
										];

		//1a. Get original data
		$branch_data				= \App\Models\Branch::findornew($branch['id']);

		//1b. Validate Basic branch Parameter
		$validator					= Validator::make($branch, $branch_rules);

		if (!$validator->passes())
		{
			$errors->add('Branch', $validator->errors());
		}
		else
		{
			//if validator passed, save branch
			$branch_data		= $branch_data->fill($branch);

			if(!$branch_data->save())
			{
				$errors->add('Branch', $branch_data->getError());
			}
		}
		//End of validate Branch


		//3. Validate branch chart Parameter
		if(!$errors->count() && isset($branch['charts']) && is_array($branch['charts']))
		{
			$chart_current_ids		= [];
			foreach ($branch['charts'] as $key => $value) 
			{
				if(!$errors->count())
				{
					$chart_data		= \App\Models\Chart::findornew($value['id']);

					$chart_rules	=	[
											'branch_id'			=> 'exists:branches,id|'.($is_new ? '' : 'in:'.$chart_data['id']),
											'chart_id'			=> 'exists:charts,id',
											'name'				=> 'required|max:255',
											'grade'				=> 'numeric',
											'tag'				=> 'required|max:255',
											'min_employee'		=> 'numeric',
											'ideal_employee'	=> 'numeric',
											'max_employee'		=> 'numeric',
										];

					$validator		= Validator::make($value, $chart_rules);

					//if there was chart and validator false
					if (!$validator->passes())
					{
						$errors->add('chart', $validator->errors());
					}
					else
					{
						$value['branch_id']				= $chart_data['id'];

						$chart_data						= $chart_data->fill($value);

						if(!$chart_data->save())
						{
							$errors->add('chart', $chart_data->getError());
						}
						else
						{
							$chart_current_ids[]		= $chart_data['id'];
						}
					}
				}

				//save follow calendars
				if(!$errors->count() && isset($value['follows']) && is_array($product['follows']))
				{
					$calendar_current_ids               = [];

					foreach ($value['follows'] as $key2 => $value2) 
					{
						$calendar                       = \App\Models\Calendar::find($value2['id']);

						if($calendar)
						{
							$calendar_current_ids[]     = $value2['id'];
						}
					}
					
					if(!$errors->count())
					{
					    if(!$chart_data->follows()->sync($calendar_current_ids))
					    {
					        $errors->add('Calendar', 'Kalender jabatan tidak tersimpan.');
					    }
					}
				}


				//save follow calendars
				if(!$errors->count() && isset($value['chartworkleaves']) && is_array($product['chartworkleaves']))
				{
					$workleave_current_ids               = [];

					foreach ($value['chartworkleaves'] as $key2 => $value2) 
					{
						$workleave                       = \App\Models\Workleave::find($value2['id']);

						if($workleave)
						{
							$workleave_current_ids[]     = $value2['id'];
						}
					}
					
					if(!$errors->count())
					{
					    if(!$chart_data->chartworkleaves()->sync($workleave_current_ids))
					    {
					        $errors->add('Workleave', 'Cuti jabatan tidak tersimpan.');
					    }
					}
				}
			}

			//if there was no error, check if there were things need to be delete
			if(!$errors->count())
			{
				$charts									= \App\Models\Chart::branchid($branch_data['id'])->get(['id'])->toArray();
				
				$chart_should_be_ids					= [];
				foreach ($charts as $key => $value) 
				{
					$chart_should_be_ids[]				= $value['id'];
				}

				$difference_chart_ids					= array_diff($chart_should_be_ids, $chart_current_ids);

				if($difference_chart_ids)
				{
					foreach ($difference_chart_ids as $key => $value) 
					{
						$chart_data						= \App\Models\Chart::find($value);

						if(!$chart_data->delete())
						{
							$errors->add('chart', $chart_data->getError());
						}
					}
				}
			}
		}
		//End of validate branch chart

		//4. Validate branch contact Parameter
		if(!$errors->count() && isset($branch['contacts']) && is_array($branch['contacts']))
		{
			$contact_current_ids		= [];
			foreach ($branch['contacts'] as $key => $value) 
			{
				if(!$errors->count())
				{
					$contact_data		= \App\Models\BranchContact::findornew($value['id']);

					$contact_rules		=	[
												'branch_id'		=> 'exists:branchs,id|'.($is_new ? '' : 'in:'.$branch_data['id']),
												'branch_type'	=> (!$contact_data ? '' : 'in:'.get_class($branch_data)),
												'item'			=> 'required|max:255',
												'is_default'	=> 'boolean',
											];

					$validator			= Validator::make($value, $contact_rules);
					

					//if there was contact and validator false
					if (!$validator->passes())
					{
						$errors->add('contact', $validator->errors());
					}
					else
					{
						$value['branch_id']			= $branch_data['id'];
						$value['branch_type']		= get_class($branch_data);

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
				$contacts							= \App\Models\BranchContact::branchid($branch['id'])->get(['id'])->toArray();
				
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
						$contact_data				= \App\Models\BranchContact::find($value);

						if(!$contact_data->delete())
						{
							$errors->add('contact', $contact_data->getError());
						}
					}
				}
			}
		}
		//End of validate branch contact

		//5. Validate branch api Parameter
		if(!$errors->count() && isset($branch['apis']) && is_array($branch['apis']))
		{
			$api_current_ids		= [];
			foreach ($branch['apis'] as $key => $value) 
			{
				if(!$errors->count())
				{
					$api_data		= \App\Models\Api::findornew($value['id']);

					$api_rules		=	[
												'branch_id'				=> 'exists:branches,id|'.($is_new ? '' : 'in:'.$api_data['id']),
												'client'				=> 'required|max:255',
												'secret'				=> 'required|max:255',
												'workstation_address'	=> 'required|max:255|unique:varians,sku,'.(!is_null($value['id']) ? $value['id'] : ''),
												'workstation_name'		=> 'required|max:255',
												'is_active'				=> 'boolean',
											];

					$validator		= Validator::make($value, $api_rules);

					//if there was api and validator false
					if (!$validator->passes())
					{
						$errors->add('Api', $validator->errors());
					}
					else
					{
						$value['branch_id']		= $api_data['id'];

						$api_data				= $api_data->fill($value);

						if(!$api_data->save())
						{
							$errors->add('Api', $api_data->getError());
						}
						else
						{
							$api_current_ids[]	= $api_data['id'];
						}
					}
				}
			}

			//if there was no error, check if there were things need to be delete
			if(!$errors->count())
			{
				$apis								= \App\Models\Api::branchid($branch_data['id'])->get(['id'])->toArray();
				
				$api_should_be_ids					= [];
				foreach ($apis as $key => $value) 
				{
					$api_should_be_ids[]			= $value['id'];
				}

				$difference_api_ids					= array_diff($api_should_be_ids, $api_current_ids);

				if($difference_api_ids)
				{
					foreach ($difference_api_ids as $key => $value) 
					{
						$api_data					= \App\Models\Api::find($value);

						if(!$api_data->delete())
						{
							$errors->add('Api', $api_data->getError());
						}
					}
				}
			}
		}
		//End of validate branch api

		//6. Validate branch fingerprint Parameter
		if(!$errors->count() && isset($branch['fingerprint']) && is_array($branch['fingerprint']))
		{
			$fp_data		= \App\Models\FingerPrint::findornew($branch['fingerprint']['id']);
			
			$fp_rule   		=   [
									'branch_id'				=> 'exists:branches,id|'.(!$fp_data ? '' : 'in:'.$branch_data['id']),
									'left_thumb'			=> 'boolean',
									'left_index_finger'		=> 'boolean',
									'left_middle_finger'	=> 'boolean',
									'left_ring_finger'		=> 'boolean',
									'left_little_finger'	=> 'boolean',
									'right_thumb'			=> 'boolean',
									'right_index_finger'	=> 'boolean',
									'right_middle_finger'	=> 'boolean',
									'right_ring_finger'		=> 'boolean',
									'right_little_finger'	=> 'boolean',
								];

			$validator   = Validator::make($branch['fingerprint'], $fp_rule);

			//if there was log and validator false
			if (!$validator->passes())
			{
				$errors->add('Log', $validator->errors());
			}
			else
			{
				$branch['fingerprint']['branch_id']		= $fp_data['id'];

				$fp_data								= $fp_data->fill($branch['fingerprint']);

				if(!$fp_data->save())
				{
					$errors->add('Log', $fp_data->getError());
				}
			}
		}
		//End of validate branch fingerprint
	}
}
