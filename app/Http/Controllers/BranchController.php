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

					$chart_rules		=	[
												'branch_id'			=> 'required|exists:branches,id|'.($is_new ? '' : 'in:'.$chart_data['id']),
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
						$value['branch_id']		= $chart_data['id'];

						$chart_data					= $chart_data->fill($value);

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
			}
			//if there was no error, check if there were things need to be delete
			if(!$errors->count())
			{
				$charts									= \App\Models\Chart::branchid($chart['id'])->get(['id'])->toArray();
				
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
						$chart_data					= \App\Models\Chart::find($value);

						if(!$chart_data->delete())
						{
							$errors->add('chart', $chart_data->getError());
						}
					}
				}
			}
		}
		//End of validate branch chart
	}
}
