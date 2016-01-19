<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIdleLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('idle_logs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('process_log_id')->unsigned()->index();
			$table->double('total_active');
			
			$table->double('total_idle');
			$table->double('total_idle_1');
			$table->double('total_idle_2');
			$table->double('total_idle_3');
			$table->double('break_idle');

			$table->integer('frequency_idle_1');
			$table->integer('frequency_idle_2');
			$table->integer('frequency_idle_3');

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
		Schema::drop('idle_logs');
	}

}
