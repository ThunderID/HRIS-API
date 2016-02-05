<?php namespace App\Http\Controllers\Tracker;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Input;

/**
 * Handle Tracker 1.*
 * 
 * @author cmooy
 */

class UpdateController extends Controller 
{
	/**
	 * Tracker update application
	 *
	 * 1. Check input
	 * 2. Check auth
	 * 3. Set current absence 
	 * @return Response
	 */
	function updateversion()
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

		//3. Set current absence 
		if((float)$attributes['application']['api']['tr_ver'] < (float)Config::get('current.absence.version'))
		{
			return Response::json('sukses|'.Config::get('current.absence.url1').'|'.Config::get('current.absence.url2'), 200);
		}

		return Response::json('200', 200);
	}
}
