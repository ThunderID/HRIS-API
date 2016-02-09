<?php namespace App\Models\Observers;

use App\Models\Employee;
use App\Models\TakenWorkleave;
use App\Models\GivenWorkleave;
use App\Models\ProcessLog;
use App\Models\AttendanceLog;
use Illuminate\Support\MessageBag;

/**
 * Used in AttendanceLog model
 *
 * @author cmooy
 */
class AttendanceLogObserver 
{
	/** 
     * observe AttendanceLog event saving
     * 1. check actual status
     * 2. check modified status
     * 3. modify count status
     * 4. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function saving($model)
	{
		$errors 							= new MessageBag;
		
		//1. check actual status
		if(!$model->processlog()->count())
		{
			if($model->margin_start==0 && $model->margin_end==0)
			{
				$actual_status 				= 'AS';
			}
			elseif($model->margin_start>=0 && $model->margin_end>=0)
			{
				$actual_status 				= 'HB';
			}
			else
			{
				if($model->process_log->schedule_start < $model->process_log->start && $model->process_log->schedule_end < $model->process_log->start)
				{
					$actual_status		= 'AS';
				}
				elseif($model->process_log->schedule_start > $model->process_log->end && $model->process_log->schedule_end > $model->process_log->end)
				{
					$actual_status		= 'AS';
				}
				else
				{
					$actual_status		= 'HC';
				}
			}

			$model->actual_status		= $actual_status;

			$current_status 			= $actual_status;
		}

		//2. check modified status
		if($model->modified_status!='')
		{
			if(strtoupper($model->actual_status)=='HB')
			{
				$errors->add('modified', 'Tidak dapat mengubah status dengan status awal HB');
			}
			elseif(strtoupper($model->actual_status)=='HC' && !in_array(strtoupper($model->modified_status), ['HT', 'HD', 'HC', 'HP']))
			{
				$errors->add('modified', 'Status untuk kedatangan cacat tidak valid. Pilih diantara status berikut : HT, HD, HC, HP');
			}
			elseif(strtoupper($model->actual_status)=='AS' && !in_array(strtoupper($model->modified_status), ['DN', 'SS', 'SL', 'CN', 'CB', 'CI', 'UL', 'AS']))
			{
				$errors->add('modified', 'Status untuk absensi tidak valid. Pilih diantara status berikut : DN, SS, SL, CN, CB, CI, UL, AS');
			}

			$model->modified_at 		= Carbon::now()->format('Y-m-d H:i:s');

			$current_status 			= $model->modified_status;
		}

		//3. modify count status
		if($model->processlog()->count())
		{
			$count 									= 1;
			$prev_data 								= ProcessLog::personid($model->processlog->person_id)->notid($model->process_log_id)->orderby('on', 'desc')->first();

			if($prev_data)
			{
				$prev_status 						= AttendanceLog::processlogid($model->process_log_id)->orderby('created_at', 'desc')->first();

				if($prev_status && $prev_status->modified_status!='')
				{
					$current_prev_status 			= $prev_status->modified_status;
				}
				elseif($prev_status)
				{
					$current_prev_status 			= $prev_status->actual_status;
				}

				if(isset($current_prev_status) && $current_status == $current_prev_status)
				{
					$count 							= $prev_status['count_status'] + 1;
				}
			}

			$model->count_status 					= $count;
		}

		if($errors->count())
		{
			$model['errors']						= $errors;

			return false;
		}

		return true;
	}

	/** 
     * observe AttendanceLog event saved
     * 1. check workleave
     * 2. check unpaid workleave
     * 3. check changed workleave
     * 4. act, accept or refuse
     * 
     * @param $model
     * @return bool
     */
	public function saved($model)
	{
		$errors 					= new MessageBag;

		if($model->processlog()->count())
		{
			$on						= $model->processlog->format('Y-m-d');
	
			//1. check workleave
			if(in_array(strtoupper($model->modified_status), ['CN','CB']))
			{
				//check if pw on that day were provided
				$taken				= TakenWorkleave::personid($model->processlog->person_id)->ondate([$on, $on])->status(strtoupper($model->modified_status))->first();
				if(!$taken)
				{
					//count left over quota
					$given			= GivenWorkleave::personid($model->processlog->person_id)->ondate([$on, null])->status('CN')->get();

					$takenw 		= 0;
					$givenw 		= 0;
					$checkp 		= -1;

					foreach ($given as $key => $value) 
					{
						$taking		= TakenWorkleave::personid($model->processlog->person_id)->ondate([$value->start->format('Y-m-d'), $value->end->format('Y-m-d')])->status('CN')->sum('amount');

						$takenw 	= $takenw + $taking;

						$givenw 	= $givenw + $value['amount'];

						if(($takenw + $givenw) > 0)
						{
							$checkp = $key;
						}
					}

					$person 							= new Employee;
					$person->workend 					= $on;
					$person 							= $person->id($model->processlog->person_id)->first();

					if($person && $checkp!=-1)
					{
						$pworkleave 					= new TakenWorkleave;
						$pworkleave->fill([
								'work_id'				=> $person->current_work_id,
								'person_id'				=> $person->id,
								'person_workleave_id'	=> $given[$checkp]->id,
								'created_by'			=> $model->modified_by,
								'name'					=> 'Pengambilan '.$pwP->name,
								'status'				=> $model->modified_status,
								'notes'					=> (isset($model->notes) ? $model->notes : ''),
								'start'					=> $on,
								'end'					=> $on,
								'quota'					=> -1
						]);

						if(!$pworkleave->save())
						{
							$errors->add('Workleave', $pworkleave->getError());
						}
					}
					//check if previosly written
					else
					{
						$prev_status 						= AttendanceLog::processlogid($model->process_log_id)->orderby('created_at', 'desc')->first();
					
						if(!$prev_status || strtoupper($prev_status->modified_status)!='UL')
						{
							$alog 						= new AttendanceLog;
							$alog->fill([
								'process_log_id'		=> $model->process_log_id,
								'margin_start'			=> $model->margin_start,
								'margin_end'			=> $model->margin_end,
								'count_status'			=> $model->count_status,
								'actual_status'			=> $model->actual_status,
								'modified_status'		=> 'UL',
								'modified_at'			=> $model->modified_at,
								'notes'					=> 'Auto generated dari attendance log, karena tidak ada cuti.',
							]);

							if(!$alog->save())
							{
								$errors->add('Workleave', $alog->getError());
							}
						}
					}
				}
			}

			//2. check unpaid workleave
			elseif(strtoupper($model->actual_status)=='AS' && (in_array(strtoupper($model->modified_status), ['UL'])))
			{
				//check if pw on that day were provided
				$taken				= TakenWorkleave::personid($model->processlog->person_id)->ondate([$on, $on])->status(strtoupper($model->modified_status))->first();
				if(!$taken)
				{
					//count left over quota
					$given			= GivenWorkleave::personid($model->processlog->person_id)->ondate([$on, null])->status('CN')->get();

					$takenw 		= 0;
					$givenw 		= 0;
					$checkp 		= -1;

					foreach ($given as $key => $value) 
					{
						$taking		= TakenWorkleave::personid($model->processlog->person_id)->ondate([$value->start->format('Y-m-d'), $value->end->format('Y-m-d')])->status('CN')->sum('amount');

						$takenw 	= $takenw + $taking;

						$givenw 	= $givenw + $value['amount'];

						if(($takenw + $givenw) > 0)
						{
							$checkp = $key;
						}
					}

					$person 							= new Employee;
					$person->workend 					= $on;
					$person 							= $person->id($model->processlog->person_id)->first();

					if($person && $checkp!=-1)
					{
						$prev_status 					= AttendanceLog::processlogid($model->process_log_id)->orderby('created_at', 'desc')->first();
					
						if(!$prev_status || strtoupper($prev_status->modified_status)!='CN')
						{
							$alog 						= new AttendanceLog;
							$alog->fill([
								'process_log_id'		=> $model->process_log_id,
								'margin_start'			=> $model->margin_start,
								'margin_end'			=> $model->margin_end,
								'count_status'			=> $model->count_status,
								'actual_status'			=> $model->actual_status,
								'modified_status'		=> 'CN',
								'modified_at'			=> $model->modified_at,
								'notes'					=> 'Auto generated dari attendance log, karena masih ada cuti.',
							]);

							if(!$alog->save())
							{
								$errors->add('Workleave', $alog->getError());
							}
						}
					}
				}
			}

			//3. check changed workleave
			$current_status			= AttendanceLog::processlogid($model->process_log_id)->orderBy('created_at', 'desc')->first();

			if($current_status && in_array($current_status->modified_status, ['CN', 'CB']) && !in_array($model->modified_status, ['CN', 'CB']))
			{
				$taken				= TakenWorkleave::personid($model->processlog->person_id)->ondate([$on, $on])->status(strtoupper($model->modified_status))->first();

				if($taken && !$taken->delete())
				{
					$errors->add('Workleave', $taken->getError());
				}
			}
		}

		if($errors->count())
		{
			$model['errors']		= $errors;

			return false;
		}

		return true;
	}
}
