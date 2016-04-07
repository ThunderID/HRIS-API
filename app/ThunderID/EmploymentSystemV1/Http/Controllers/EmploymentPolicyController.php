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
	 * @param code and id
	 * @return $nik
	 */			
	public function generateNIK($code, $id = 0) 
	{
		//1. get join date
		$start_work		= Work::personid($id)->chartorganisationcode($code)->orderby('start', 'desc')->first();

		if($start_work)
		{
			$join_year 	= $start_work->start;
		}
		else
		{
			$join_year 	= Carbon::now();
		}

		$nik 			= $code.$join_year->format('y').'.';

		//2. get last nik order number
		$last_nik 		= Work::selectraw('max(nik)')->where('nik', 'like', $nik.'%')->chartorganisationcode($code)->notpersonid($id)->first();

		if($last_nik)
		{
			$number		= 1 + (int)substr($last_nik['nik'],6);
		}
		else
		{
			$number 	= 1;
		}

		$generated_nik 	= $nik . str_pad($number,3,"0",STR_PAD_LEFT);

		return new JSend('success', ['nik' => $generated_nik]);
    }


}
