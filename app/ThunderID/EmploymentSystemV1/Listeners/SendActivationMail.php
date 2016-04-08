<?php

namespace App\ThunderID\EmploymentSystemV1\Listeners;

use App\ThunderID\EmploymentSystemV1\Events\EmployeeCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Listener for Product Viewed
 * 
 * @author cmooy
 */
class SendActivationMail
{
	/**
	 * Create the event listener.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

	/**
	 * Handle the event.
	 *
	 * 1. check the receiver
	 * 2. send mail
	 * 3. if there were no error save
	 *
	 * @param  ProductSearched  $event
	 * @return void
	 */
	public function handle(EmployeeCreated $event)
	{
		//1. check the receiver
		$data						= ['employee' => $event->person];

		//send mail
		Mail::send('mail.account.activation', ['data' => $data], function($message) use($event)
		{
			$message->to($event->person['email'], $event->person['name'])->subject('AKTIVASI AKUN HRIS');
		}); 
		
	}
}