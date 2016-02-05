<?php namespace App\Http\Controllers\Tracker;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Input;

/**
 * Handle Tracker version 0.* & 1.*
 * 
 * @author cmooy
 */

class LoginController extends Controller 
{
	/**
	 * Tracker admin login
	 *
	 * 1. Check input
	 * 2. Check auth
	 * 3. Check tracker version
	 * 4. Parsing data variable
	 * 5. Check work auth
	 * @return Response
	 */
	function absence()
	{
		$attributes 							= Input::only('application');

		//1. Check input
		if(!$attributes['application'])
		{
			return Response::json('101', 200);
		}		

		if(!isset($attributes['application']['api']['client']) || !isset($attributes['application']['api']['secret']) || !isset($attributes['application']['api']['tr_ver']) || !isset($attributes['application']['api']['station_id']) || !isset($attributes['application']['api']['email']) || !isset($attributes['application']['api']['password']))
		{
			return Response::json('102', 200);
		}

		//2. Check auth
		$client 								= \App\Models\Api::client($attributes['application']['api']['client'])->secret($attributes['application']['api']['secret'])->workstationaddress($attributes['application']['api']['station_id'])->with(['branch'])->first();
		
		if(!$client)
		{
	        $filename                       	= storage_path().'/logs/appid.log';
			$fh                             	= fopen($filename, 'a+'); 
			$template 							= date('Y-m-d H:i:s : Login : ').json_encode($attributes['application']['api'])."\n";
	        fwrite($fh, $template); 
	        fclose($fh);

			return Response::json('402', 200);
		}

		//3. Check tracker version
		if(strtolower($attributes['application']['api']['tr_ver'])!=$client['tr_version'])
		{
			$result->tr_version 				= strtolower($attributes['application']['api']['tr_ver']);
			
			if(!$result->save())
			{
				return Response::json('301', 200);
			}
		}

		//4. Parsing data variable
		$organisationid 						= $content->data->branch->organisation_id;

		$email 									= $attributes['application']['api']['email'];
		$password 								= $attributes['application']['api']['password'];
		
		$check									= Auth::attempt(['email' => $email, 'password' => $password]);

		//5. Check work auth
		if($check)
		{
			$employee 							= \App\Models\Employee::id(Auth::user()['id'])->first();

			if(!$employee)
			{
				return Response::json('403', 200);
			}

			$workauth 							= \App\Models\WorkAuthentication::menuid(102)->workid($employee['work_id'])->organisationid($organisationid)->orderby('tmp_auth_group_id', 'asc')->first();

			if((!$workauth))
			{
				return Response::json('403', 200);
			}
			else
			{
				return Response::json('Sukses', 200);
			}
		}
		else
		{
			return Response::json('404', 200);
		}

		return Response::json('404', 200);
	}
}
