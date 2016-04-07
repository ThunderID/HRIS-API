<?php

namespace App\Libraries;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;

/**
 * Class libraries to validate and parse policy
 *
 * @author cmooy
 */
class ValidatorOfDocument
{
	protected $errors;

	public function __construct()
	{
		$this->errors = new MessageBag;
	}

	/**
	 * validate input parameter (need to parse) based on policy code
	 *
	 * @param array of policy (contain code)
	 * @return boolean
	 */
	public function validate($array_of_document)
	{
		$rules  				= [];

		switch ($array_of_document['code']) 
		{
			case 'ktp':
					$rules 		= [
									'nomor_ktp' 	=> 'required|max:255',
									'berlaku_hingga' => 'date_format:"Y-m-d H:i:s"',
								  ];
				break;
			case 'pendidikan_terakhir':
					$rules 		= [
									'sekolah' 		=> 'required|max:255',
									'jenjang' 		=> 'max:255',
									'jurusan' 		=> 'max:255',
								  ];
				break;
			case 'sertifikasi':
					$rules 		= [
									'nama'				=> 'required|max:255',
									'penyelenggara'		=> 'required|max:255',
									'tempat' 			=> 'max:255',
									'tanggal_mulai'		=> 'date_format:"Y-m-d H:i:s"',
									'tanggal_selesai'	=> 'date_format:"Y-m-d H:i:s"',
									'is_certified'		=> 'boolean',
								  ];
				break;
			case 'npwp':
					$rules 		= [
									'npwp'				=> 'required|max:255',
								  ];
				break;
			case 'bpjs_kesehatan':
					$rules 		= [
									'nomor_peserta'		=> 'required|max:255',
								  ];
				break;
			case 'bpjs_ketenagakerjaan':
					$rules 		= [
									'nomor_peserta'		=> 'required|max:255',
								  ];
				break;
			case 'info_medis':
					$rules 		= [
									'golongan_darah'	=> 'required|max:2',
									'tanggal_checkup'	=> 'date_format:"Y-m-d H:i:s"',
									'hasil_checkup'		=> 'required"',
								  ];
				break;
			case 'akun_bank':
					$rules 		= [
									'jenis_rekening'	=> 'required|max:255',
									'nama_bank'			=> 'required|max:255',
									'nomor_rekening'	=> 'required|max:255',
								  ];
				break;
			case 'reksa_dana':
					$rules 		= [
									'jenis_rekening'	=> 'required|max:255',
									'nama_reksadana'	=> 'required|max:255',
									'nomor_rekening'	=> 'required|max:255',
								  ];
				break;
			default:
				$this->errors->add('Code', 'Dokumen tidak terdaftar.');
				break;
		}

		if($this->errors->count())
		{
			return false;
		}
	
		$validator					= Validator::make($array_of_document, $rules);

		if (!$validator->passes())
		{
			$this->errors->add('Code', $validator->errors());
			
			return false;
		}

		return true;
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