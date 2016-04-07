<?php

namespace App\ThunderID\EmploymentSystemV1\Http\Controllers;

use App\Libraries\JSend;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use \App\ThunderID\EmploymentSystemV1\Models\Work;

class EmploymentPolicyController extends Controller
{
	/**
	 * auto generate nik
	 *
	 * 1. get join date
	 * 2. get last nik order number
	 * 3. check if user already hath nik
	 * @param code and id
	 * @return $nik
	 */			
	public function generateNIK($code, $id = 0, $join_year = null) 
	{
		//1. get join date
		$start_work		= Work::personid($id)->chartorganisationcode($code)->orderby('start', 'desc')->first();

		if(!is_null($join_year))
		{
			$join_year 	= $join_year;
		}
		elseif($start_work)
		{
			$join_year 	= $start_work->start->format('y');
		}
		else
		{
			$join_year 	= Carbon::now()->format('y');
		}

		$nik 			= $code.$join_year.'.';

		//2. get last nik order number
		$last_nik 		= Work::selectraw('max(nik)')->where('nik', 'like', $nik.'%')->chartorganisationcode($code)->first();

		if($last_nik)
		{
			$number		= 1 + (int)substr($last_nik['max(nik)'],6);
		}
		else
		{
			$number 	= 1;
		}

		$generated_nik 	= $nik . str_pad($number,3,"0",STR_PAD_LEFT);

		//3. check if user already hath nik
		if($start_work && !empty($start_work['nik']))
		{
			$generated_nik 	= $start_work['nik'];
		}

		return new JSend('success', ['nik' => $generated_nik]);
    }


}
