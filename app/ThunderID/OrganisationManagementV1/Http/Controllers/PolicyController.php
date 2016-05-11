<?php

namespace App\ThunderID\OrganisationManagementV1\Http\Controllers;

use ThunderID\APIHelper\Data\Jsend;
use App\Libraries\PolicyOfOrganisation as POO;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * Handle Protected Resource of policy
 * 
 * @author cmooy
 */
class PolicyController extends Controller
{
	/**
	 * Display a Policy of an org
	 *
	 * @return Response
	 */
	public function index($org_id = null)
	{
		$result						= new \App\ThunderID\OrganisationManagementV1\Models\Policy;

		$result 					= $result->organisationid($org_id);

		if(Input::has('search'))
		{
			$search					= Input::get('search');

			foreach ($search as $key => $value) 
			{
				switch (strtolower($key)) 
				{
					case 'code' :
						$result 	= $result->code($value);
						break;
					case 'ondate' :
						$result 	= $result->ondate($value);
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
					case 'newest':
						$result     = $result->orderby('started_at', $value);
						break;
					case 'code':
						$result     = $result->orderby($key, $value);
						break;
					case 'name':
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
	 * store a Policy of an org
	 *
	 * 1. Save Policy
	 * 2. Save Contacs
	 * @return Response
	 */
	public function store($org_id = null)
	{
		if(!Input::has('policy'))
		{
			return new JSend('error', (array)Input::all(), 'Tidak ada data Policy.');
		}

		$errors						= new MessageBag();
		$contact					= [];

		DB::beginTransaction();

		//1. Validate Policy Parameter
		$policy						= Input::get('policy');

		if(is_null($policy['id']))
		{
			$is_new					= true;
		}
		else
		{
			$is_new					= false;
		}

		$policy_rules				=	[
											'code'				=> 'required',
										];

		//1a. Get original data
		$policy_data				= \App\ThunderID\OrganisationManagementV1\Models\Policy::findornew($policy['id']);

		//1b. Validate Basic Policy Parameter
		$validator					= Validator::make($policy, $policy_rules);

		if (!$validator->passes())
		{
			$errors->add('Policy', $validator->errors());
		}
		else
		{
			$validating_policy 		= new POO;

			if(!$validating_policy->validate($policy))
			{
				$errors->add('Policy', $validating_policy->getError());
			}
			else
			{
				//if validator passed, save Policy
				$validated_policy 				= new POO;
				$validated_policy 				= $validated_policy->parse($policy);

				$policy_data['organisation_id']	= $org_id;
				$policy_data					= $policy_data->fill($validated_policy);

				if(!$policy_data->save())
				{
					$errors->add('Policy', $policy_data->getError());
				}
			}
		}
		//End of validate Policy

		if($errors->count())
		{
			DB::rollback();

			return new JSend('error', (array)Input::all(), $errors);
		}

		DB::commit();
		
		$final_Policy			= \App\ThunderID\OrganisationManagementV1\Models\Policy::id($policy_data['id'])->organisationid($org_id)->first()->toArray();

		return new JSend('success', (array)$final_Policy);
	}


	/**
	 * Delete a Policy
	 *
	 * @return Response
	 */
	public function delete($org_id = null, $id = null)
	{
		//
		$policy					= \App\ThunderID\OrganisationManagementV1\Models\Policy::id($id)->organisationid($org_id)->first();

		if(!$policy)
		{
			return new JSend('error', (array)Input::all(), 'Policy tidak ditemukan.');
		}

		$result					= $policy->toArray();

		if($policy->delete())
		{
			return new JSend('success', (array)$result);
		}

		return new JSend('error', (array)$result, $policy->getError());
	}
}
