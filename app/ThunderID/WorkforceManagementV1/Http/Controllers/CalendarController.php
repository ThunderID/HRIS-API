<?php

namespace App\ThunderID\WorkforceManagementV1\Http\Controllers;

use ThunderID\APIHelper\Data\JSend;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * Handle Protected Resource of calendar
 * 
 * @author cmooy
 */
class CalendarController extends Controller
{
		/**
	 * Display all Organisations
	 *
	 * @param search, skip, take
	 * @return JSend Response
	 */
	public function index($org_id)
	{
		$result						= new \App\ThunderID\WorkforceManagementV1\Models\Calendar;

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
		else
		{
			$result->orderby('name', 'asc');
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
	 * Display a calendar of an org
	 *
	 * @return Response
	 */
	public function detail($org_id = null, $id = null)
	{
		$result						= \App\ThunderID\WorkforceManagementV1\Models\Calendar::id($id)->organisationid($org_id)->with(['organisation', 'charts'])->first();

		if($result)
		{
			return new JSend('success', (array)$result->toArray());
		}

		return new JSend('error', (array)Input::all(), 'ID Tidak Valid.');
	}

	/**
	 * store a calendar of an org
	 *
	 * 1. Save calendar
	 * 2. Save Contacs
	 * @return Response
	 */
	public function store($org_id = null)
	{
		if(!Input::has('calendar'))
		{
			return new JSend('error', (array)Input::all(), 'Tidak ada data calendar.');
		}

		$errors						= new MessageBag();
		$contact					= [];

		DB::beginTransaction();

		//1. Validate calendar Parameter
		$calendar					= Input::get('calendar');

		if(is_null($calendar['id']))
		{
			$is_new					= true;
		}
		else
		{
			$is_new					= false;
		}

		$calendar_rules				=	[
											'organisation_id'	=> 'exists:hrom_organisations,id|'.($is_new ? '' : 'in:'.$org_id),
											'name'				=> 'required|max:255',
											'phone'				=> 'max:20',
											'email'				=> 'max:255',
										];

		//1a. Get original data
		$calendar_data				= \App\ThunderID\WorkforceManagementV1\Models\Calendar::findornew($calendar['id']);

		//1b. Validate Basic calendar Parameter
		$validator					= Validator::make($calendar, $calendar_rules);

		if (!$validator->passes())
		{
			$errors->add('calendar', $validator->errors());
		}
		else
		{
			//if validator passed, save calendar
			$calendar_data['organisation_id']	= $org_id;
			$calendar_data					= $calendar_data->fill($calendar);

			if(!$calendar_data->save())
			{
				$errors->add('calendar', $calendar_data->getError());
			}
		}
		//End of validate calendar

		//2. Validate calendar contact Parameter
		if(!$errors->count())
		{
			if(isset($calendar['address']))
			{
				$prev_contact		= \App\ThunderID\WorkforceManagementV1\Models\Contact::contactableid($calendar_data['id'])->contactabletype(get_class($calendar_data))->type('address')->default(true)->first();
				
				if($prev_contact['value']!=$calendar['address'])
				{
					$contact			= new \App\ThunderID\WorkforceManagementV1\Models\Contact;

					$contact->fill([
							'contactable_id'	=> $calendar_data['id'],
							'contactable_type'	=> get_class($calendar_data),
							'type'				=> 'address',
							'value'				=> $calendar['address'],
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
				$contact					= \App\ThunderID\WorkforceManagementV1\Models\Contact::contactableid($calendar_data['id'])->contactabletype(get_class($calendar_data))->type('address')->default(true)->first();

				if($contact)
				{
					$contact->is_default	= false;

					if(!$contact->save())
					{
						$errors->add('Contact', $contact->getError());
					}
				}
			}

			if(isset($calendar['phone']))
			{
				$prev_contact		= \App\ThunderID\WorkforceManagementV1\Models\Contact::contactableid($calendar_data['id'])->contactabletype(get_class($calendar_data))->type('phone')->default(true)->first();
				
				if($prev_contact['value']!=$calendar['phone'])
				{
					$contact			= new \App\ThunderID\WorkforceManagementV1\Models\Contact;

					$contact->fill([
							'contactable_id'	=> $calendar_data['id'],
							'contactable_type'	=> get_class($calendar_data),
							'type'				=> 'phone',
							'value'				=> $calendar['phone'],
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
				$contact					= \App\ThunderID\WorkforceManagementV1\Models\Contact::contactableid($calendar_data['id'])->contactabletype(get_class($calendar_data))->type('phone')->default(true)->first();

				if($contact)
				{
					$contact->is_default	= false;

					if(!$contact->save())
					{
						$errors->add('Contact', $contact->getError());
					}
				}
			}

			if(isset($calendar['email']))
			{
				$prev_contact		= \App\ThunderID\WorkforceManagementV1\Models\Contact::contactableid($calendar_data['id'])->contactabletype(get_class($calendar_data))->type('email')->default(true)->first();
				
				if($prev_contact['value']!=$calendar['email'])
				{
					$contact			= new \App\ThunderID\WorkforceManagementV1\Models\Contact;

					$contact->fill([
							'contactable_id'	=> $calendar_data['id'],
							'contactable_type'	=> get_class($calendar_data),
							'type'				=> 'email',
							'value'				=> $calendar['email'],
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
				$contact					= \App\ThunderID\WorkforceManagementV1\Models\Contact::contactableid($calendar_data['id'])->contactabletype(get_class($calendar_data))->type('email')->default(true)->first();

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
		//End of validate calendar contact
		
		if($errors->count())
		{
			DB::rollback();

			return new JSend('error', (array)Input::all(), $errors);
		}

		DB::commit();
		
		$final_calendar			= \App\ThunderID\WorkforceManagementV1\Models\Calendar::id($calendar_data['id'])->organisationid($org_id)->with(['charts'])->first()->toArray();

		return new JSend('success', (array)$final_calendar);
	}


	/**
	 * Delete a calendar
	 *
	 * @return Response
	 */
	public function delete($org_id = null, $id = null)
	{
		//
		$calendar					= \App\ThunderID\WorkforceManagementV1\Models\Calendar::id($id)->organisationid($org_id)->with(['charts'])->first();

		if(!$calendar)
		{
			return new JSend('error', (array)Input::all(), 'Kantor Cabang tidak ditemukan.');
		}

		$result					= $calendar->toArray();

		if($calendar->delete())
		{
			return new JSend('success', (array)$result);
		}

		return new JSend('error', (array)$result, $calendar->getError());
	}
}
