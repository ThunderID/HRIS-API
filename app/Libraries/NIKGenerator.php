<?php

namespace App\Libraries;

use Illuminate\Support\MessageBag;
use App\ThunderID\EmploymentSystemV1\Models\Work;
use Carbon\Carbon;

/**
 * Class libraries to validate and parse policy
 *
 * @author cmooy
 */
class NIKGenerator
{
	protected $errors;

	public function __construct()
	{
		$this->errors = new MessageBag;
	}

	/**
	 * validate input parameter (need to parse) based on policy code
	 *	 
	 * 1. get join date
	 * 2. get last nik order number
	 * 3. check if user already hath nik
	 * @param array of policy (contain code)
	 * @return boolean
	 */
	public static function generate($code, $id, $join_year)
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

		return $generated_nik;
	}

	/**
	 * getting protected error
	 *
	 * @return message bag
	 */
	public function getError()
	{
		return $this->errors;
	}
}