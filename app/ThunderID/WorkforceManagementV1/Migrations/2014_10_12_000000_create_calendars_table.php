<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalendarsTable extends Migration 
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection(env('DB_CONNECTION_WORKFORCE', 'mysql'))->create('hrwm_calendars', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('organisation_id')->unsigned()->index();
			$table->string('name', 255);
			$table->text('workdays');
			$table->date('ondate');
			$table->time('start');
			$table->time('end');
			$table->text('break_idle');
			$table->timestamps();
			$table->softDeletes();

			$table->index(['deleted_at', 'organisation_id', 'ondate']);
			$table->index(['deleted_at', 'ondate']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('hrwm_calendars');
	}

}
