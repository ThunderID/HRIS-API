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
									'nomorktp' 		=> 'required|max:255',
									'berlakuhingga' => 'date_format:"Y-m-d H:i:s"',
								  ];
				break;
			case 'pendidikanterakhir':
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
									'tanggalmulai'		=> 'date_format:"Y-m-d H:i:s"',
									'tanggalselesai'	=> 'date_format:"Y-m-d H:i:s"',
									'is_certified'		=> 'boolean',
								  ];
				break;
			case 'npwp':
					$rules 		= [
									'npwp'			=> 'required|max:255',
								  ];
				break;
			case 'bpjskesehatan':
					$rules 		= [
									'nomorpeserta'	=> 'required|max:255',
								  ];
				break;
			case 'bpjsketenagakerjaan':
					$rules 		= [
									'nomorpeserta'	=> 'required|max:255',
								  ];
				break;
			case 'infomedis':
					$rules 		= [
									'golongandarah'	=> 'required|max:2',
									'tanggalcheckup'=> 'date_format:"Y-m-d H:i:s"',
									'hasilcheckup'	=> 'required"',
								  ];
				break;
			case 'akunbank':
					$rules 		= [
									'jenisrekening'	=> 'required|max:255',
									'namabank'		=> 'required|max:255',
									'nomorrekening'	=> 'required|max:255',
								  ];
				break;
			case 'reksadana':
					$rules 		= [
									'jenisrekening'	=> 'required|max:255',
									'namareksadana'	=> 'required|max:255',
									'nomorrekening'	=> 'required|max:255',
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