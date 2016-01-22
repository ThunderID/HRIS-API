<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use DB;
use \Illuminate\Support\MessageBag as MessageBag;

/**
 * store and delete schedule from queues
 *
 * @return boolean
 * @author cmooy
 **/
class ScheduleCommand extends Command 
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name 		= 'hr:schedules';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Batch Schedule Command.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		//
		$id 					= $this->argument()['queueid'];

		if($this->option('queuefunc'))
		{
			$queuefunc 			= $this->option('queuefunc');
			switch ($queuefunc) 
			{
				case 'delete':
					$result 	= $this->delete($id);
					break;
			
				default:
					$result 	= $this->store($id);
					break;
			}
		}
		else
		{
			$result				= $this->store($id);
		}

		return $result;
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['queueid', InputArgument::REQUIRED, 'An example argument.'],
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
            array('queuefunc', null, InputOption::VALUE_OPTIONAL, 'Queue Function', null),
        );
	}

	/**
	 * store schedule and update for persons
	 *
	 * @return void
	 **/
	public function store($id)
	{
		$queue 						= new \App\Models\Queue;
		$pending 					= $queue->find($id);

		$parameters 				= json_decode($pending->parameter, true);
		$messages 					= json_decode($pending->message, true);

		$errors 					= new MessageBag;

		$data 						= \App\Models\Schedule::ondate($parameters['on'])->calendarid($parameters['calendar_id'])->first();

		if($data)
		{
			$schedule_data			= $data;
		}
		else
		{
			$schedule_data			= new \App\Models\Schedule;
		}

		$schedule_data->fill($parameters);

		if(!$schedule_data->save())
		{
			$errors->add('Batch', $schedule_data->getError());
		}

		if(!$errors->count())
		{
			$morphed 						= new \App\Models\QueueMorph;
			$morphed->fill([
				'queue_id'					=> $id,
				'queue_morph_id'			=> $schedule_data->id,
				'queue_morph_type'			=> get_class($schedule_data),
			]);

			$morphed->save();

			$pnumber 						= $pending->total_process;

			$messages['message'][$pnumber] 	= 'Sukses Menyimpan Jadwal '.(isset($schedule_data['calendar']['name']) ? $schedule_data['calendar']['name'] : '');
			
			$pending->fill(['process_number' => $pnumber, 'message' => 'Sukses']);
		}
		else
		{
			$pnumber 						= $pending->total_process;
			
			$messages['message'][$pnumber] 	= 'Gagal Menyimpan Jadwal '.(isset($schedule_data['calendar']['name']) ? $schedule_data['calendar']['name'] : '');
			
			$messages['errors'][$pnumber] 	= $errors;

			$pending->fill(['process_number' => $pnumber, 'message' => 'Sukses']);
		}

		$pending->save();

		return true;
	}

	/**
	 * delete schedule and update for persons
	 *
	 * @return void
	 **/
	public function delete($id)
	{
		$queue 						= new \App\Models\Queue;
		$pending 					= $queue->find($id);

		$parameters 				= json_decode($pending->parameter, true);
		$messages 					= json_decode($pending->message, true);

		$errors 					= new MessageBag;

		$schedule_data 				= \App\Models\Schedule::ondate($parameters['on'])->calendarid($parameters['calendar_id'])->first();

		if(!$schedule_data)
		{
			$errors->add('Batch', 'Tidak ada schedule.');
		}
		elseif(!$schedule_data->delete())
		{
			$errors->add('Batch', $schedule_data->getError());
		}

		if(!$errors->count())
		{
			$pnumber 						= $pending->total_process;

			$messages['message'][$pnumber] 	= 'Sukses Menghapus Jadwal '.(isset($schedule_data['calendar']['name']) ? $schedule_data['calendar']['name'] : '');
			
			$pending->fill(['process_number' => $pnumber, 'message' => 'Sukses']);
		}
		else
		{
			$pnumber 						= $pending->total_process;
			
			$messages['message'][$pnumber] 	= 'Gagal Menghapus Jadwal '.(isset($schedule_data['calendar']['name']) ? $schedule_data['calendar']['name'] : '');
			
			$messages['errors'][$pnumber] 	= $errors;

			$pending->fill(['process_number' => $pnumber, 'message' => 'Sukses']);
		}

		$pending->save();

		return true;
	}
}
