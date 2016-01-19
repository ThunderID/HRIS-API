<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmpQueuesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tmp_queues', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('created_by')->unsigned()->index();
			$table->string('process_name', 255);
			$table->string('process_option', 255);
			$table->text('parameter');
			$table->integer('total_process');
			$table->integer('task_per_process');
			$table->integer('process_number');
			$table->integer('total_task');
			$table->text('message');
			$table->timestamps();
			$table->softDeletes();
			
			$table->index(['deleted_at', 'process_number', 'process_name']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tmp_queues');
	}

}
