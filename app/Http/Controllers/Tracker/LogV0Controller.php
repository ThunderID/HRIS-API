<?php namespace App\Http\Controllers\Tracker;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;

/**
 * Handle Tracker version 0.* inputs
 * 
 * @author cmooy
 */
class LogV0Controller extends Controller 
{
	/**
	 * Store logs
	 *
	 * 1. Check input
	 * 2. Check auth
	 * 3. Check tracker version
	 * 4. Check data log
	 * 5. Simpan data log
	 * @return Response
	 */
	public function store()
	{
		dd(Response::json('101', 200));
		$attributes 							= Input::only('application', 'log');

		//1. Check input
		if(!$attributes['application'])
		{
			return Response::json('101', 200);
		}

		if(!isset($attributes['application']['api']['client']) || !isset($attributes['application']['api']['secret']) || !isset($attributes['application']['api']['station_id']))
		{
			return Response::json('102', 200);
		}	

		//2. Check auth
		$results 								= \App\Models\Api::client($attributes['application']['api']['client'])->secret($attributes['application']['api']['secret'])->workstationaddress($attributes['application']['api']['station_id'])->with(['branch'])->first();
		
		if(!$results)
		{
			$filename                       	= storage_path().'/logs/appid.log';
			$fh                             	= fopen($filename, 'a+'); 
			$template 							= date('Y-m-d H:i:s : Log : ').json_encode($attributes['application']['api'])."\n";
	        fwrite($fh, $template); 
	        fclose($fh);

			return Response::json('402', 200);
		}

		//3. Check tracker version
		if(isset($attributes['application']['api']['tr_ver']) && strtolower($attributes['application']['api']['tr_ver'])!=$result['tr_version'])
		{
			$result->tr_version 				= strtolower($attributes['application']['api']['tr_ver']);

			if(!$result->save())
			{
				return Response::json('301', 200);
			}
		}

		$organisationid 						= $result->branch->organisation_id;

		//4. Check data log
		if(!$attributes['log'])
		{
			return Response::json('103', 200);
		}

		//5. Simpan data log
		DB::beginTransaction();

		if(isset($attributes['log']))
		{
			$attributes['log']					= (array)$attributes['log'];
			foreach ($attributes['log'] as $key => $value) 
			{
				$log['name']					= strtolower($value[1]);
				$log['on']						= date("Y-m-d H:i:s", strtotime($value[2]));
				$log['pc']						= $value[3];
				$person							= \App\Models\Person::username($value[0])->first();

				//5a. check is log belongs to existed user
				if(!$person)
				{
					//5b. store log to error if not
					$log['email']				= $value[0];
					$log['message']				= 'User tidak terdaftar';
					$log['organisation_id']		= $organisationid;
					$log['ip'] 					= $_SERVER['REMOTE_ADDR'];
					$saved_error_log 			= new \App\Models\ErrorLog;
					$saved_error_log->fill($log);
					$saved_error_log->save();
				}
				else
				{
					//5b. store log to log if yes
					$log['person_id']			= $person['id'];
					$saved_log 					= new \App\Models\Log;
					$saved_log->fill($log);

					if(!$saved_log->save())
					{
						$log['email']			= $value[0];
						$log['message']			= json_encode($saved_log->getError());
						$log['ip'] 				= $_SERVER['REMOTE_ADDR'];
						$saved_error_log 		= new \App\Models\ErrorLog;
						$saved_error_log->fill($log);
						$saved_error_log->save();
					}
				}
			}
		}

		DB::commit();
		
		return Response::json('Sukses', 200);
	}
}
