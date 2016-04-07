<?php

namespace App\ThunderID\EmploymentSystemV1\Http\Controllers;

use App\Libraries\JSend;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * Handle Protected Resource of ContractElement
 * 
 * @author cmooy
 */
class ContractElementController extends Controller
{
	/**
	 * Display a ContractElement of an org
	 *
	 * @return Response
	 */
	public function index($org_id = null)
	{
		$result						= new \App\ThunderID\EmploymentSystemV1\Models\ContractElement;

		$result 					= $result->organisationid($org_id);

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
	 * store a ContractElement of an org
	 *
	 * 1. Save ContractElement
	 * 2. Save Contacs
	 * @return Response
	 */
	public function store($org_id = null)
	{
		if(!Input::has('ContractElement'))
		{
			return new JSend('error', (array)Input::all(), 'Tidak ada data ContractElement.');
		}

		$errors						= new MessageBag();
		$contact					= [];

		DB::beginTransaction();

		//1. Validate ContractElement Parameter
		$ContractElement						= Input::get('ContractElement');

		if(is_null($ContractElement['id']))
		{
			$is_new					= true;
		}
		else
		{
			$is_new					= false;
		}

		$ContractElement_rules				=	[
											'code'				=> 'required',
										];

		//1a. Get original data
		$ContractElement_data				= \App\ThunderID\OrganisationManagementV1\Models\ContractElement::findornew($ContractElement['id']);

		//1b. Validate Basic ContractElement Parameter
		$validator					= Validator::make($ContractElement, $ContractElement_rules);

		if (!$validator->passes())
		{
			$errors->add('ContractElement', $validator->errors());
		}
		else
		{
			$validating_ContractElement 		= new POO;

			if(!$validating_ContractElement->validate($ContractElement))
			{
				$errors->add('ContractElement', $validating_ContractElement->getError());
			}
			else
			{
				//if validator passed, save ContractElement
				$validated_ContractElement 				= new POO;
				$validated_ContractElement 				= $validated_ContractElement->parse($ContractElement);

				$ContractElement_data['organisation_id']	= $org_id;
				$ContractElement_data					= $ContractElement_data->fill($validated_ContractElement);

				if(!$ContractElement_data->save())
				{
					$errors->add('ContractElement', $ContractElement_data->getError());
				}
			}
		}
		//End of validate ContractElement

		if($errors->count())
		{
			DB::rollback();

			return new JSend('error', (array)Input::all(), $errors);
		}

		DB::commit();
		
		$final_ContractElement			= \App\ThunderID\OrganisationManagementV1\Models\ContractElement::id($ContractElement_data['id'])->organisationid($org_id)->first()->toArray();

		return new JSend('success', (array)$final_ContractElement);
	}


	/**
	 * Delete a ContractElement
	 *
	 * @return Response
	 */
	public function delete($org_id = null, $id = null)
	{
		//
		$ContractElement					= \App\ThunderID\OrganisationManagementV1\Models\ContractElement::id($id)->organisationid($org_id)->first();

		if(!$ContractElement)
		{
			return new JSend('error', (array)Input::all(), 'ContractElement tidak ditemukan.');
		}

		$result					= $ContractElement->toArray();

		if($ContractElement->delete())
		{
			return new JSend('success', (array)$result);
		}

		return new JSend('error', (array)$result, $ContractElement->getError());
	}
}
