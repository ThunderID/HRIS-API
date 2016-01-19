<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProcessLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('process_logs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('person_id')->unsigned()->index();
			$table->integer('work_id')->unsigned()->index();
			$table->string('name', 255);
			$table->date('on');
			$table->time('start');
			$table->time('end');
			$table->time('fp_start');
			$table->time('fp_end');
			$table->time('schedule_start');
			$table->time('schedule_end');
			$table->text('tooltip');
			$table->timestamps();
			$table->softDeletes();
			
			$table->index(['deleted_at', 'person_id', 'on']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('process_logs');
	}

}
