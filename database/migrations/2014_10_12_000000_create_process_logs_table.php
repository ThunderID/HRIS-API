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
			$table->integer('modified_by')->unsigned()->index();
			$table->string('name', 255);
			$table->date('on');
			$table->time('start');
			$table->time('end');
			$table->time('fp_start');
			$table->time('fp_end');
			$table->time('schedule_start');
			$table->time('schedule_end');
			$table->double('margin_start');
			$table->double('margin_end');
			$table->double('total_idle');
			$table->double('total_idle_1');
			$table->double('total_idle_2');
			$table->double('total_idle_3');
			$table->integer('frequency_idle_1');
			$table->integer('frequency_idle_2');
			$table->integer('frequency_idle_3');
			$table->double('total_sleep');
			$table->double('total_active');
			$table->string('actual_status', 255);
			$table->string('modified_status', 255);
			$table->double('tolerance_time');
			$table->text('tooltip');
			$table->datetime('modified_at')->nullable();
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
