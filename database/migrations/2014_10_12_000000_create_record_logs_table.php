<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecordLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('record_logs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('parent_id')->unsigned()->index();
			$table->integer('person_id')->unsigned()->index();
			$table->integer('record_log_id')->unsigned()->index();
			$table->string('record_log_type', 255);
			$table->string('name', 255);
			$table->integer('level');
			$table->enum('action', ['save', 'delete', 'restore']);
			$table->text('notes');
			$table->text('old_attributes');
			$table->text('new_attributes');
			$table->timestamps();
			$table->softDeletes();
			
			$table->index(['deleted_at', 'person_id']);
			$table->index(['deleted_at', 'table_type']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('record_logs');
	}

}
