<?php

namespace App\ThunderID\EmploymentSystemV1\Http\Controllers;

use ThunderID\APIHelper\Data\JSend;
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
		if(!Input::has('contractelement'))
		{
			return new JSend('error', (array)Input::all(), 'Tidak ada data contractelement.');
		}

		$errors						= new MessageBag();
		$contact					= [];

		DB::beginTransaction();

		//1. Validate contract_element Parameter
		$contract_element						= Input::get('contractelement');

		if(is_null($contract_element['id']))
		{
			$is_new					= true;
		}
		else
		{
			$is_new					= false;
		}

		$contract_element_rules		=	[
											'organisation_id'	=> 'exists:hrom_organisations,id|'.($is_new ? '' : 'in:'.$org_id),
											'name'				=> 'required|max:255',
										];

		//1a. Get original data
		$contract_element_data				= \App\ThunderID\EmploymentSystemV1\Models\ContractElement::findornew($contract_element['id']);

		//1b. Validate Basic contract_element Parameter
		$validator					= Validator::make($contract_element, $contract_element_rules);

		if (!$validator->passes())
		{
			$errors->add('contract_element', $validator->errors());
		}
		else
		{
			$contract_element_data['organisation_id']	= $org_id;
			$contract_element_data						= $contract_element_data->fill($contract_element);

			if(!$contract_element_data->save())
			{
				$errors->add('contract_element', $contract_element_data->getError());
			}
		}
		//End of validate contract_element

		if($errors->count())
		{
			DB::rollback();

			return new JSend('error', (array)Input::all(), $errors);
		}

		DB::commit();
		
		$final_ContractElement			= \App\ThunderID\EmploymentSystemV1\Models\ContractElement::id($contract_element_data['id'])->organisationid($org_id)->first()->toArray();

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
		$contract_element					= \App\ThunderID\EmploymentSystemV1\Models\ContractElement::id($id)->organisationid($org_id)->first();

		if(!$contract_element)
		{
			return new JSend('error', (array)Input::all(), 'Contract Element tidak ditemukan.');
		}

		$result					= $contract_element->toArray();

		if($contract_element->delete())
		{
			return new JSend('success', (array)$result);
		}

		return new JSend('error', (array)$result, $contract_element->getError());
	}
}
