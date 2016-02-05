<?php namespace App\Http\Controllers\Tracker;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;

/**
 * Handle Tracker version 0.* & 1.*
 * 
 * @author cmooy
 */

class TestDeviceController extends Controller 
{
	/**
	 * Tracker test
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
		//1. Check input
		$attributes 							= Input::only('application');

		if(!$attributes['application'])
		{
			return Response::json('101', 200);
		}		

		if(!isset($attributes['application']['api']['client']) || !isset($attributes['application']['api']['secret']) || !isset($attributes['application']['api']['tr_ver']) || !isset($attributes['application']['api']['station_id']))
		{
			return Response::json('102', 200);
		}

		//2. Check auth
		$client 								= \App\Models\Api::client($attributes['application']['api']['client'])->secret($attributes['application']['api']['secret'])->workstationaddress($attributes['application']['api']['station_id'])->with(['branch'])->first();

		if(!$client)
		{
			$filename                       	= storage_path().'/logs/appid.log';
			$fh                             	= fopen($filename, 'a+'); 
			$template 							= date('Y-m-d H:i:s : Test : ').json_encode($attributes['application']['api'])."\n";
	        fwrite($fh, $template); 
	        fclose($fh);

			return Response::json('401', 200);
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

		return Response::json('Sukses', 200);
	}

	/**
	 * Tracker check time
	 *
	 * 1. Check input
	 * 2. Check auth
	 * 3. Return current time
	 * @return Response
	 */
	function absencetime()
	{
		//1. Check input
		$attributes 							= Input::only('application');

		if(!$attributes['application'])
		{
			return Response::json('101', 200);
		}		

		if(!isset($attributes['application']['api']['client']) || !isset($attributes['application']['api']['secret']) || !isset($attributes['application']['api']['tr_ver']) || !isset($attributes['application']['api']['station_id']))
		{
			return Response::json('102', 200);
		}

		//2. Check auth
		$client 								= \App\Models\Api::client($attributes['application']['api']['client'])->secret($attributes['application']['api']['secret'])->workstationaddress($attributes['application']['api']['station_id'])->with(['branch'])->first();

		if(!$client)
		{
			$filename                       	= storage_path().'/logs/appid.log';
			$fh                             	= fopen($filename, 'a+'); 
			$template 							= date('Y-m-d H:i:s : Test : ').json_encode($attributes['application']['api'])."\n";
	        fwrite($fh, $template); 
	        fclose($fh);

			return Response::json('401', 200);
		}

		//3. Return current time
		$date 									= Carbon::now();

		return Response::json('sukses|'.$date->format('Y/m/d H:i:s'), 200);
	}
}
