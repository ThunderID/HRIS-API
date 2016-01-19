<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attendance_details', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('attendance_log_id')->unsigned()->index();
			$table->integer('person_workleave_id')->unsigned()->index();
			$table->integer('person_document_id')->unsigned()->index();
			$table->string('person_workleave_type', 255);
			$table->string('person_document_type', 255);
			$table->timestamps();
			$table->softDeletes();

			$table->index(['deleted_at', 'person_workleave_id']);
			$table->index(['deleted_at', 'person_document_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('attendance_details');
	}

}
