<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attendance_logs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('process_log_id')->unsigned()->index();
			$table->integer('settlement_by')->unsigned()->index();
			$table->integer('modified_by')->unsigned()->index();
			$table->string('actual_status', 255);
			$table->string('modified_status', 255);

			$table->double('margin_start');
			$table->double('margin_end');

			$table->double('tolerance_time');
			
			$table->double('count_status');
			$table->text('notes');
			$table->datetime('modified_at')->nullable();
			$table->datetime('settlement_at')->nullable();

			$table->timestamps();
			$table->softDeletes();
			
			$table->index(['deleted_at']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('attendance_logs');
	}

}
