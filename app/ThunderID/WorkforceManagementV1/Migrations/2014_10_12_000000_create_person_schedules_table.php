<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonSchedulesTable extends Migration 
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection(env('DB_CONNECTION_WORKFORCE', 'mysql'))->create('hrwm_person_schedules', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('person_id')->unsigned()->index();
			$table->string('name', 255);
			$table->date('ondate');
			$table->time('start');
			$table->time('end');
			$table->double('break_idle');
			$table->string('status', 255);
			$table->timestamps();
			$table->softDeletes();

			$table->index(['deleted_at', 'person_id', 'ondate']);
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
		Schema::drop('hrwm_person_schedules');
	}

}
