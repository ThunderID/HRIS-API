<?php

namespace App\ThunderID\EmploymentSystemV1\Http\Controllers;

use ThunderID\APIHelper\Data\Jsend;
use App\Libraries\UsernameGenerator;
use App\Libraries\NIKGenerator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use \App\ThunderID\EmploymentSystemV1\Models\Work;
use \App\ThunderID\EmploymentSystemV1\Models\Employee;

class EmploymentAttributeController extends Controller
{
	/**
	 * auto generate nik
	 *
	 * @param code and id
	 * @return $nik
	 */			
	public function generateNIK($code, $id = 0, $join_year = null) 
	{
		$nik 	 		= NIKGenerator::generate($code, $id, $join_year);

		return new JSend('success', ['nik' => $nik]);
    }

	/**
	 * auto generate username
	 *
	 * 1. check existance
	 * 2. get firstname
	 * @param code and employee name
	 * @return $username
	 */			
	public function generateUsername($code, $id = 0) 
	{
		//1. check existance
		$uname			= Employee::id($id)->first();

		if($uname && !empty($uname['username']))
		{
			return new JSend('success', ['username' => $uname['username']]);
		}
			
		//2. get firstname
		if(!Input::has('name'))
		{
			return new JSend('error', (array)Input::all(), 'No Name');
		}
		
		$name 			= Input::get('name');

		$username 	 	= UsernameGenerator::generate($code, $id, $name);

		return new JSend('success', ['username' => $username]);
    }

	/**
	 * auto generate template document
	 *
	 * @return $array of document
	 */			
	public function getDocumentTemplate() 
	{
		$templates	= 	[
							'ktp' 					=> 	[
															'nomor_ktp',
															'berlaku_hingga',
													  	],
				
							'pendidikan_terakhir' 	=> [
															'sekolah',
															'jenjang',
															'jurusan',
													  	],
				
							'sertifikasi' 			=> 	[
															'nama',
															'penyelenggara',
															'tempat',
															'tanggal_mulai',
															'tanggal_selesai',
															'is_certified',
														],
				
			 				'npwp' 					=> 	[
															'npwp',
														],
				
							'bpjs_kesehatan'			=> 	[
															'nomor_peserta',
														],
				
							'bpjs_ketenagakerjaan' 	=>	[
															'nomor_peserta',
														],
				
			 				'info_medis'			=> 	[
															'golongan_darah',
															'tanggal_checkup',
															'hasil_checkup',
														],
				
							'akun_bank' 			=> 	[
															'jenis_rekening',
															'nama_bank',
															'nomor_rekening',
													  	],
				
							'reksa_dana' 			=>	[
															'jenis_rekening',
															'nama_reksadana',
															'nomor_rekening',
														],
						];

		return new JSend('success', ['templates' => $templates]);
    }
}
