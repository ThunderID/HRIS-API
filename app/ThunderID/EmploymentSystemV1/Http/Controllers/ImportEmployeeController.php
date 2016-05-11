<?php

namespace App\ThunderID\EmploymentSystemV1\Http\Controllers;

use ThunderID\APIHelper\Data\Jsend;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Libraries\UsernameGenerator;
use App\Libraries\NIKGenerator;
use Carbon\Carbon;
use \App\ThunderID\OrganisationManagementV1\Models\Organisation;
use \App\ThunderID\OrganisationManagementV1\Models\Branch;
use \App\ThunderID\OrganisationManagementV1\Models\Chart;
use \App\ThunderID\EmploymentSystemV1\Models\Employee;
use \App\ThunderID\EmploymentSystemV1\Models\Work;
use \App\ThunderID\PersonSystemV1\Models\Person;
use \App\ThunderID\PersonSystemV1\Models\PersonDocument;
use \App\ThunderID\PersonSystemV1\Models\Relative;
use \App\ThunderID\PersonSystemV1\Models\Contact;
use \App\ThunderID\PersonSystemV1\Models\MaritalStatus;

use App\ThunderID\EmploymentSystemV1\Events\EmployeeCreated;
use App\Libraries\ValidatorOfDocument as VOD;

class ImportEmployeeController extends Controller
{
	/**
	 * auto generate template document
	 *
	 * @return $array of document
	 */			
	public function get() 
	{
		$profile 	= 	[
							'name',
							'date_of_birth',
							'place_of_birth',
							'gender',
							'marital_status',
							'email',
							'phone',
							'address',
						]; 
		$work 		= 	[
							'kode_perusahaan',
							'nik',
							'kantor',
							'jabatan',
							'grade',
							'status_kerja',
							'tanggal_mulai_status',
							'tanggal_akhir_status',
							'alasan_akhir_status',
						]; 
		$relative 	= 	[
							[
								'name',
								'date_of_birth',
								'place_of_birth',
								'gender',
								'relationship',
								'email',
								'phone',
								'address',
							],
							[
								'name',
								'date_of_birth',
								'place_of_birth',
								'gender',
								'relationship',
								'email',
								'phone',
								'address',
							]
						]; 
		$document	= 	[
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


		$template 	= ['profile' => $profile, 'work' => $work, 'relatives' => $relative, 'document' => $document];

		return new JSend('success', ['employee' => $template]);
    }

    /**
	 * post import document
	 *
	 * 1. check organisation
	 * 2. generate username
	 * 3. save profile
	 * @return $array of document
	 */			
	public function post() 
	{
		set_time_limit(0);
		
		if(!Input::has('employee'))
		{
			return new JSend('error', (array)Input::all(), 'Tidak ada data employee.');
		}

		$data 			= Input::get('employee');
		$globalnotify	= [];

		foreach ($data as $col => $row) 
		{
			$errors								= new MessageBag();

			$organisation 						= Organisation::code($row['work']['kode_perusahaan'])->first();
			if($organisation)
			{
				DB::beginTransaction();

				$send_mail 						= false;

				//2. generate username
				$username 						= UsernameGenerator::generate($row['work']['kode_perusahaan'], 0, $row['profile']['name']);
				
				$row['profile']['username'] 	= $username;

				//3. save profile
				$person								= new Employee;
				$row['profile']['date_of_birth'] 	= Carbon::parse($row['profile']['date_of_birth'])->format('Y-m-d H:i:s');

				$person->fill($row['profile']);

				if(!$person->save())
				{
					$errors->add('profile', $person->getError());
				}

				//4. save contact
				if(!$errors->count())
				{
					if(!empty($row['email']))
					{
						$contact				= new Contact;
						$contact->fill(
								[
									'contactable_id'	=> $person['id'],
									'contactable_type'	=> get_class($person),
									'type'				=> 'email',
									'value'				=> $row['email'],
									'is_default'		=> true,
								]
							);

						if(!$contact->save())
						{
							$errors->add('profile', $contact->getError());
						}

						$send_mail 				= true;
					}

					if(!empty($row['phone']))
					{
						$contact				= new Contact;
						$contact->fill(
								[
									'contactable_id'	=> $person['id'],
									'contactable_type'	=> get_class($person),
									'type'				=> 'phone',
									'value'				=> $row['phone'],
									'is_default'		=> true,
								]
							);

						if(!$contact->save())
						{
							$errors->add('profile', $contact->getError());
						}
					}

					if(!empty($row['address']))
					{
						$contact				= new Contact;
						$contact->fill(
								[
									'contactable_id'	=> $person['id'],
									'contactable_type'	=> get_class($person),
									'type'				=> 'address',
									'value'				=> $row['address'],
									'is_default'		=> true,
								]
							);

						if(!$contact->save())
						{
							$errors->add('profile', $contact->getError());
						}
					}
				}

				//5. save marital status
				if(!$errors->count())
				{
					if(!empty($row['marital_status']))
					{
						$marital				= new MaritalStatus;
						$marital->fill(
								[
									'person_id'	=> $person['id'],
									'status'	=> $row['marital_status'],
									'ondate'	=> Carbon::now()->format('Y-m-d H:i:s'),
								]
							);

						if(!$marital->save())
						{
							$errors->add('profile', $marital->getError());
						}
					}
				}

				//6. save work
				if(!$errors->count())
				{
					//6a. check branch
					$branch 				= Branch::organisationid($organisation['id'])->name($row['work']['kantor'])->first();

					if(!$branch)
					{
						$branch 			= new Branch;

						$branch->fill(
							[
								'name'				=> $row['work']['kantor'],
								'organisation_id'	=> $organisation['id'],
							]
						);

						if(!$branch->save())
						{
							$errors->add('work', $branch->getError());
						}
					}

					//6b. check chart
					if(!$errors->count())
					{
						$chart 				= Chart::branchid($branch['id'])->name($row['work']['jabatan'])->first();

						if(!$chart)
						{
							$chart 			= new Chart;

							$chart->fill(
								[
									'name'			=> $row['work']['jabatan'],
									'branch_id'		=> $branch['id'],
								]
							);

							if(!$chart->save())
							{
								$errors->add('work', $chart->getError());
							}
						}
					}

					//6c. save work
					if(!$errors->count())
					{
						$join_year 			= Carbon::parse($row['work']['tanggal_mulai_status'])->format('y');

						$nik 				= NIKGenerator::generate($organisation['code'], $person['id'], $join_year);

						$row['work']['chart_id']		= $chart['id'];
						$row['work']['person_id']		= $person['id'];
						$row['work']['nik']				= $nik;
						$row['work']['status']			= $row['work']['status_kerja'];
						$row['work']['start']			= Carbon::parse($row['work']['tanggal_mulai_status'])->format('Y-m-d H:i:s');
						
						if(strtotime($row['work']['tanggal_akhir_status']))
						{
							$row['work']['end']			= Carbon::parse($row['work']['tanggal_akhir_status'])->format('Y-m-d H:i:s');
						}
						
						$row['work']['reason_end_job']	= $row['work']['alasan_akhir_status'];

						$work 				= new Work;

						$work->fill($row['work']);

						if(!$work->save())
						{
							$errors->add('work', $work->getError());
						}
					}
				}

				//7. save relative
				foreach ($row['relatives'] as $key => $value) 
				{
					if(!$errors->count() && empty($value))
					{
						$relative 				= new Person;
						$value['date_of_birth']	= Carbon::parse($value['date_of_birth'])->format('Y-m-d H:i:s');

						$relative->fill($value);

						if(!$relative->save())
						{
							$errors->add('relatives', $relative->getError());
						}
						else
						{
							$relation 			= new Relative;

							$relation->fill(['relative_id' => $relative['id'], 'person_id' => $person['id'], 'relationship' => $value['relationship']]);

							if(!$relation->save())
							{
								$errors->add('relative', $relation->getError());
							}
						}
					}
				}

				//8. save document
				foreach ($row['document'] as $key => $value) 
				{
					if(!$errors->count())
					{
						$document 				= new PersonDocument;

						$doc['person_id']		= $person['id'];
						$doc['documents']		= array_merge(['code' => $key], ['document' => $value]);

						$validating_document 	= new VOD;

						if(!$validating_document->validate($doc['documents']))
						{
							$errors->add('PersonDocument', $validating_document->getError());
						}
						else
						{
							$doc['documents']	=json_encode($doc['documents']);
							$document			= $document->fill($doc);

							if(!$document->save())
							{
								$errors->add('PersonDocument', $document->getError());
							}
						}
					}
				}

				if(!$errors->count())
				{
					DB::commit();
				
					$globalnotify[$col] 		= json_encode(['status' => 'sukses', 'data' => $col, 'message' => ['Data Karyawan Tersimpan']]);
					
					if($send_mail)
					{
						Event::fire(new EmployeeCreated($final_employee));
					}
				}
				else
				{
					DB::rollback();

					$globalnotify[$col] 		= json_encode(['status' => 'error', 'data' => $col, 'message' => $errors]);
				}
			}
			else
			{
				$globalnotify[$col] 			= json_encode(['status' => 'error', 'data' => $col, 'message' => ['Kode Perusahaan tidak valid']]);
			}
		}

		return new JSend('success', $globalnotify);
    }
}

