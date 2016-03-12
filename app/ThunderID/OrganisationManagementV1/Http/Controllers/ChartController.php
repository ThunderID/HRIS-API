<?php

namespace App\ThunderID\OrganisationManagementV1\Http\Controllers;

use App\Libraries\JSend;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * Handle Protected Resource of Chart
 * 
 * @author cmooy
 */
class ChartController extends Controller
{
	/**
	 * Display all Charts
	 *
	 * @param search, skip, take
	 * @return JSend Response
	 */
	public function index($org_id = null, $branch_id = null)
	{
		//check branch
		$branch 					= \App\ThunderID\OrganisationManagementV1\Models\Branch::id($branch_id)->organisationid($org_id)->first();

		if(!$branch)
		{
			\App::abort(404);
		}

		$result						= new \App\ThunderID\OrganisationManagementV1\Models\Chart;

		$result 					= $result->branchid($branch_id);

		if(Input::has('search'))
		{
			$search					= Input::get('search');

			foreach ($search as $key => $value) 
			{
				switch (strtolower($key)) 
				{
					case 'name' :
						$result 	= $result->name($value);
						break;
					case 'department' :
						$result 	= $result->department($value);
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
					case 'path':
						$result     = $result->orderby($key, $value);
						break;
					case 'name':
						$result     = $result->orderby($key, $value);
						break;
					case 'department':
						$result     = $result->orderby($key, $value);
						break;
					default:
						# code...
						break;
				}
			}
		}
		else
		{
			$result     			= $result->orderby('path', 'desc');
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

		$result						= $result->with(['chart'])->get()->toArray();

		return new JSend('success', (array)['count' => $count, 'data' => $result]);
	}

	/**
	 * Display a branch of an org
	 *
	 * @return Response
	 */
	public function detail($org_id = null, $branch_id = null, $id = null)
	{
		$result						= \App\ThunderID\OrganisationManagementV1\Models\Chart::id($id)->branchid($branch_id)->with(['branch'])->first();

		if($result)
		{
			return new JSend('success', (array)$result->toArray());
		}

		return new JSend('error', (array)Input::all(), 'ID Tidak Valid.');
	}

	/**
	 * Store a Chart
	 * 1. store Chart
	 *
	 * @param Chart
	 * @return Response
	 */
	public function store($org_id = null, $branch_id = null)
	{
		//check branch
		$branch 					= \App\ThunderID\OrganisationManagementV1\Models\Branch::id($branch_id)->organisationid($org_id)->first();

		if(!$branch)
		{
			\App::abort(404);
		}

		if(!Input::has('chart'))
		{
			return new JSend('error', (array)Input::all(), 'Tidak ada data Chart.');
		}

		$errors						= new MessageBag();

		DB::beginTransaction();

		//1. Validate Chart Parameter
		$chart						= Input::get('chart');

		if(is_null($chart['id']))
		{
			$is_new					= true;
		}
		else
		{
			$is_new					= false;
		}

		$chart_rules				=	[
											'branch_id'			=> 'exists:branches,id|'.($is_new ? '' : 'in:'.$branch_id),
											'name'				=> 'required|max:255',
											'department'		=> 'required|max:255',
										];

		//1a. Get original data
		$chart_data					= \App\ThunderID\OrganisationManagementV1\Models\Chart::findornew($chart['id']);

		//1b. Validate Basic Chart Parameter
		$validator					= Validator::make($chart, $chart_rules);

		if (!$validator->passes())
		{
			$errors->add('Chart', $validator->errors());
		}
		else
		{
			//if validator passed, save Chart
			$chart['branch_id']		= $branch_id;

			if(isset($chart['path']))
			{
				unset($chart['path']);
			}

			$chart_data				= $chart_data->fill($chart);

			if(!$chart_data->save())
			{
				$errors->add('Chart', $chart_data->getError());
			}
		}
		//End of validate Chart

		if($errors->count())
		{
			DB::rollback();

			return new JSend('error', (array)Input::all(), $errors);
		}

		DB::commit();
		
		$final_Chart				= \App\ThunderID\OrganisationManagementV1\Models\Chart::id($chart_data['id'])->with(['chart'])->first()->toArray();

		return new JSend('success', (array)$final_Chart);
	}

	/**
	 * Delete an Chart
	 *
	 * @return Response
	 */
	public function delete($org_id = null, $branch_id = null, $id = null)
	{
		//check branch
		$branch 					= \App\ThunderID\OrganisationManagementV1\Models\Branch::id($branch_id)->organisationid($org_id)->first();

		if(!$branch)
		{
			\App::abort(404);
		}

		$chart						= \App\ThunderID\OrganisationManagementV1\Models\Chart::branchid($branch_id)->id($id)->with(['chart'])->first();

		if(!$chart)
		{
			return new JSend('error', (array)Input::all(), 'Chart tidak ditemukan.');
		}

		$result						= $chart->toArray();

		if($chart->delete())
		{
			return new JSend('success', (array)$result);
		}

		return new JSend('error', (array)$result, $chart->getError());
	}
}
