<?php

namespace App\ThunderID\OrganisationManagementV1\Http\Controllers;

use App\Libraries\JSend;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * Handle Protected Resource of organisation
 * 
 * @author cmooy
 */
class OrganisationController extends Controller
{
	/**
	 * Display all Organisations
	 *
	 * @param search, skip, take
	 * @return JSend Response
	 */
	public function index()
	{
		$result						= new \App\ThunderID\OrganisationManagementV1\Models\Organisation;

		if(Input::has('search'))
		{
			$search					= Input::get('search');

			foreach ($search as $key => $value) 
			{
				switch (strtolower($key)) 
				{
					case 'code' :
						$result 		= $result->code($value);
						break;
					case 'name' :
						$result 		= $result->name($value);
						break;
					case 'branches' :
						$result 		= $result->with(['branches']);
					case 'charts' :
						$result 		= $result->with(['branches', 'branches.charts']);
						break;
					case 'policies' :
						$result 		= $result->with(['policies']);
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
					case 'code':
						$result     = $result->orderby($key, $value);
						break;
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

		$result						= $result->get()->toArray();

		return new JSend('success', (array)['count' => $count, 'data' => $result]);
	}

	/**
	 * Display an Organisation
	 *
	 * @param id
	 * @return Response
	 */
	public function detail($id = null)
	{
		//
		$result						= \App\ThunderID\OrganisationManagementV1\Models\Organisation::id($id)->with(['branches', 'policies'])->first();

		if($result)
		{
			return new JSend('success', (array)$result->toArray());
		}

		return new JSend('error', (array)Input::all(), 'ID Tidak Valid.');
	}

	/**
	 * Store an organisation
	 * 1. store organisation
	 * 2. store branch
	 *
	 * @param organisation
	 * @return Response
	 */
	public function store()
	{
		if(!Input::has('organisation'))
		{
			return new JSend('error', (array)Input::all(), 'Tidak ada data Organisation.');
		}

		$errors						= new MessageBag();

		DB::beginTransaction();

		//1. Validate Organisation Parameter
		$organisation				= Input::get('organisation');

		if(is_null($organisation['id']))
		{
			$is_new					= true;
		}
		else
		{
			$is_new					= false;
		}

		$organisation_rules			=	[
											'name'			=> 'required|max:255',
											'code'			=> 'required|max:255',
										];

		//1a. Get original data
		$organisation_data			= \App\ThunderID\OrganisationManagementV1\Models\Organisation::findornew($organisation['id']);

		//1b. Validate Basic Organisation Parameter
		$validator					= Validator::make($organisation, $organisation_rules);

		if (!$validator->passes())
		{
			$errors->add('Organisation', $validator->errors());
		}
		else
		{
			//if validator passed, save Organisation
			$organisation_data		= $organisation_data->fill($organisation);

			if(!$organisation_data->save())
			{
				$errors->add('Organisation', $organisation_data->getError());
			}
		}
		//End of validate Organisation

		if($errors->count())
		{
			DB::rollback();

			return new JSend('error', (array)Input::all(), $errors);
		}

		DB::commit();
		
		$final_organisation			= \App\ThunderID\OrganisationManagementV1\Models\Organisation::id($organisation_data['id'])->with(['branches', 'policies'])->first()->toArray();

		return new JSend('success', (array)$final_organisation);
	}

	/**
	 * Delete an Organisation
	 *
	 * @return Response
	 */
	public function delete($id = null)
	{
		//
		$organisation				= \App\ThunderID\OrganisationManagementV1\Models\Organisation::id($id)->with(['branches', 'policies'])->first();

		if(!$organisation)
		{
			return new JSend('error', (array)Input::all(), 'Organisasi tidak ditemukan.');
		}

		$result					= $organisation->toArray();

		if($organisation->delete())
		{
			return new JSend('success', (array)$result);
		}

		return new JSend('error', (array)$result, $organisation->getError());
	}
}
