<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use App\Models\Queue;
use App\Models\PointLog;

use DB, Carbon\Carbon;

use \Illuminate\Support\MessageBag as MessageBag;

class PointExpireCommand extends Command {
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name 		= 'point:expire';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description 	= 'Running send mail for point expire reminder.';

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
		$id 			= $this->argument()['queueid'];

		$result 		= $this->pointexpire($id);

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
	 * update 1st version
	 *
	 * @return void
	 * @author 
	 **/
	public function pointexpire($id)
	{
		$queue 						= new Queue;
		$pending 					= $queue->find($id);

		$parameters 				= json_decode($pending->parameter, true);
		$messages 					= json_decode($pending->message, true);

		$errors 					= new MessageBag;

		//check work active on that day, please consider if that queue were written days
		$points 					= PointLog::debit(true)->onactive([Carbon::parse($parameters['on'])->startOfDay()->format('Y-m-d H:i:s'), Carbon::parse($parameters['on'])->endOfDay()->format('Y-m-d H:i:s')])->haventgetcut(true)->with(['user'])->get()->toArray();

		foreach ($points as $idx => $point) 
		{
			$data					= ['point' => $point, 'balin' => $parameters['store']];

			//send mail
			Mail::send('mail.'.$parameters['template'].'.point.reminder', ['data' => $data], function($message) use($point, $parameters)
			{
				$message->to($point['user']['email'], $point['user']['name'])->subject(strtoupper($parameters['template']).' - POINT REMINDER');
			}); 

			$pnumber 						= $pending->process_number + 1;
			$messages['message'][$pnumber] 	= 'Sukses Mengirim Email '.(isset($point['user']['name']) ? $point['user']['name'] : '');
			$pending->fill(['process_number' => $pnumber, 'message' => json_encode($messages)]);
			$pending->save();
		}

		return true;
	}
}
