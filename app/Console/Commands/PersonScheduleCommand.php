<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use DB;
use \Illuminate\Support\MessageBag as MessageBag;

/**
 * store and delete Personschedule from queues
 *
 * @return boolean
 * @author cmooy
 **/
class PersonScheduleCommand extends Command 
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name 		= 'hr:personschedule';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Batch PersonSchedule Command.';

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
	 * store Personschedule and update for persons
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

		$data 						= \App\Models\PersonSchedule::ondate($parameters['on'])->personid($parameters['person_id'])->with(['person'])->first();

		if($data)
		{
			$personschedule_data			= $data;
		}
		else
		{
			$personschedule_data			= new \App\Models\PersonSchedule;
		}

		$personschedule_data->fill($parameters);

		if(!$personschedule_data->save())
		{
			$errors->add('Batch', $personschedule_data->getError());
		}

		if(!$errors->count())
		{
			$morphed 						= new \App\Models\QueueMorph;
			$morphed->fill([
				'queue_id'					=> $id,
				'queue_morph_id'			=> $personschedule_data->id,
				'queue_morph_type'			=> get_class($personschedule_data),
			]);

			$morphed->save();

			$pnumber 						= $pending->total_process;

			$messages['message'][$pnumber] 	= 'Sukses Menyimpan Jadwal '.(isset($personschedule_data['person']['name']) ? $personschedule_data['person']['name'] : '');
			
			$pending->fill(['process_number' => $pnumber, 'message' => 'Sukses']);
		}
		else
		{
			$pnumber 						= $pending->total_process;
			
			$messages['message'][$pnumber] 	= 'Gagal Menyimpan Jadwal '.(isset($personschedule_data['person']['name']) ? $personschedule_data['person']['name'] : '');
			
			$messages['errors'][$pnumber] 	= $errors;

			$pending->fill(['process_number' => $pnumber, 'message' => 'Sukses']);
		}

		$pending->save();

		return true;
	}

	/**
	 * delete Personschedule and update for persons
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

		$personschedule_data		= \App\Models\PersonSchedule::id($parameters['id'])->with(['person'])->first();

		if(!$personschedule_data)
		{
			$errors->add('Batch', 'Tidak ada Personschedule.');
		}
		elseif(!$personschedule_data->delete())
		{
			$errors->add('Batch', $personschedule_data->getError());
		}

		if(!$errors->count())
		{
			$pnumber 						= $pending->total_process;

			$messages['message'][$pnumber] 	= 'Sukses Menghapus Jadwal '.(isset($personschedule_data['person']['name']) ? $personschedule_data['person']['name'] : '');
			
			$pending->fill(['process_number' => $pnumber, 'message' => 'Sukses']);
		}
		else
		{
			$pnumber 						= $pending->total_process;
			
			$messages['message'][$pnumber] 	= 'Gagal Menghapus Jadwal '.(isset($personschedule_data['person']['name']) ? $personschedule_data['person']['name'] : '');
			
			$messages['errors'][$pnumber] 	= $errors;

			$pending->fill(['process_number' => $pnumber, 'message' => 'Sukses']);
		}

		$pending->save();

		return true;
	}
}
