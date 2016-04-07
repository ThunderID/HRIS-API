<?php

namespace App\ThunderID\OrganisationManagementV1\Http\Controllers;

use App\Libraries\JSend;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * Handle Protected Resource of branch
 * 
 * @author cmooy
 */
class BranchController extends Controller
{
		/**
	 * Display all Organisations
	 *
	 * @param search, skip, take
	 * @return JSend Response
	 */
	public function index($org_id)
	{
		$result						= new \App\ThunderID\OrganisationManagementV1\Models\Branch;
		$result 					= $result->organisationid($org_id);

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
					case 'charts' :
						$result 		= $result->with(['charts']);
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
					default:
						# code...
						break;
				}
			}
		}

		$count						= count($result->get());

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
	 * Display a branch of an org
	 *
	 * @return Response
	 */
	public function detail($org_id = null, $id = null)
	{
		$result						= \App\ThunderID\OrganisationManagementV1\Models\Branch::id($id)->organisationid($org_id)->with(['organisation', 'charts'])->first();

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
	 * 2. Save Contacs
	 * @return Response
	 */
	public function store($org_id = null)
	{
		if(!Input::has('branch'))
		{
			return new JSend('error', (array)Input::all(), 'Tidak ada data branch.');
		}

		$errors						= new MessageBag();
		$contact					= [];

		DB::beginTransaction();

		//1. Validate branch Parameter
		$branch						= Input::get('branch');

		if(is_null($branch['id']))
		{
			$is_new					= true;
		}
		else
		{
			$is_new					= false;
		}

		$branch_rules				=	[
											'organisation_id'	=> 'exists:hrom_organisations,id|'.($is_new ? '' : 'in:'.$org_id),
											'name'				=> 'required|max:255',
											'phone'				=> 'max:20',
											'email'				=> 'max:255',
										];

		//1a. Get original data
		$branch_data				= \App\ThunderID\OrganisationManagementV1\Models\Branch::findornew($branch['id']);

		//1b. Validate Basic branch Parameter
		$validator					= Validator::make($branch, $branch_rules);

		if (!$validator->passes())
		{
			$errors->add('Branch', $validator->errors());
		}
		else
		{
			//if validator passed, save branch
			$branch_data['organisation_id']	= $org_id;
			$branch_data					= $branch_data->fill($branch);

			if(!$branch_data->save())
			{
				$errors->add('Branch', $branch_data->getError());
			}
		}
		//End of validate Branch

		//2. Validate branch contact Parameter
		if(!$errors->count())
		{
			if(isset($branch['address']))
			{
				$prev_contact		= \App\ThunderID\OrganisationManagementV1\Models\Contact::contactableid($branch_data['id'])->contactabletype(get_class($branch_data))->type('address')->default(true)->first();
				
				if($prev_contact['value']!=$branch['address'])
				{
					$contact			= new \App\ThunderID\OrganisationManagementV1\Models\Contact;

					$contact->fill([
							'contactable_id'	=> $branch_data['id'],
							'contactable_type'	=> get_class($branch_data),
							'type'				=> 'address',
							'value'				=> $branch['address'],
							'is_default'		=> true,
						]);
					
					if(!$contact->save())
					{
						$errors->add('Contact', $contact->getError());
					}
				}
			}
			else
			{
				$contact					= \App\ThunderID\OrganisationManagementV1\Models\Contact::contactableid($branch_data['id'])->contactabletype(get_class($branch_data))->type('address')->default(true)->first();

				if($contact)
				{
					$contact->is_default	= false;

					if(!$contact->save())
					{
						$errors->add('Contact', $contact->getError());
					}
				}
			}

			if(isset($branch['phone']))
			{
				$prev_contact		= \App\ThunderID\OrganisationManagementV1\Models\Contact::contactableid($branch_data['id'])->contactabletype(get_class($branch_data))->type('phone')->default(true)->first();
				
				if($prev_contact['value']!=$branch['phone'])
				{
					$contact			= new \App\ThunderID\OrganisationManagementV1\Models\Contact;

					$contact->fill([
							'contactable_id'	=> $branch_data['id'],
							'contactable_type'	=> get_class($branch_data),
							'type'				=> 'phone',
							'value'				=> $branch['phone'],
							'is_default'		=> true,
						]);
					
					if(!$contact->save())
					{
						$errors->add('Contact', $contact->getError());
					}
				}
			}
			else
			{
				$contact					= \App\ThunderID\OrganisationManagementV1\Models\Contact::contactableid($branch_data['id'])->contactabletype(get_class($branch_data))->type('phone')->default(true)->first();

				if($contact)
				{
					$contact->is_default	= false;

					if(!$contact->save())
					{
						$errors->add('Contact', $contact->getError());
					}
				}
			}

			if(isset($branch['email']))
			{
				$prev_contact		= \App\ThunderID\OrganisationManagementV1\Models\Contact::contactableid($branch_data['id'])->contactabletype(get_class($branch_data))->type('email')->default(true)->first();
				
				if($prev_contact['value']!=$branch['email'])
				{
					$contact			= new \App\ThunderID\OrganisationManagementV1\Models\Contact;

					$contact->fill([
							'contactable_id'	=> $branch_data['id'],
							'contactable_type'	=> get_class($branch_data),
							'type'				=> 'email',
							'value'				=> $branch['email'],
							'is_default'		=> true,
						]);
					
					if(!$contact->save())
					{
						$errors->add('Contact', $contact->getError());
					}
				}
			}
			else
			{
				$contact					= \App\ThunderID\OrganisationManagementV1\Models\Contact::contactableid($branch_data['id'])->contactabletype(get_class($branch_data))->type('email')->default(true)->first();

				if($contact)
				{
					$contact->is_default	= false;

					if(!$contact->save())
					{
						$errors->add('Contact', $contact->getError());
					}
				}
			}
		}
		//End of validate branch contact
		
		if($errors->count())
		{
			DB::rollback();

			return new JSend('error', (array)Input::all(), $errors);
		}

		DB::commit();
		
		$final_branch			= \App\ThunderID\OrganisationManagementV1\Models\Branch::id($branch_data['id'])->organisationid($org_id)->with(['charts'])->first()->toArray();

		return new JSend('success', (array)$final_branch);
	}


	/**
	 * Delete a branch
	 *
	 * @return Response
	 */
	public function delete($org_id = null, $id = null)
	{
		//
		$branch					= \App\ThunderID\OrganisationManagementV1\Models\Branch::id($id)->organisationid($org_id)->with(['charts'])->first();

		if(!$branch)
		{
			return new JSend('error', (array)Input::all(), 'Kantor Cabang tidak ditemukan.');
		}

		$result					= $branch->toArray();

		if($branch->delete())
		{
			return new JSend('success', (array)$result);
		}

		return new JSend('error', (array)$result, $branch->getError());
	}
}
