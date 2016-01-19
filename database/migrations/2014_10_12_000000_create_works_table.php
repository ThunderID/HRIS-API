<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('works', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('chart_id')->unsigned()->index();
			$table->integer('person_id')->unsigned()->index();
			$table->integer('calendar_id')->unsigned()->index();
			$table->enum('status', ['contract', 'probation', 'internship', 'permanent', 'others', 'previous', 'admin']);
			$table->date('start');
			$table->date('end')->nullable();
			$table->string('position', 255);
			$table->string('organisation', 255);
			$table->text('reason_end_job');
			$table->boolean('is_absence');
			$table->timestamps();
			$table->softDeletes();
			
			$table->index(['deleted_at', 'person_id', 'end', 'status']);
			$table->index(['start']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('works');
	}

}
