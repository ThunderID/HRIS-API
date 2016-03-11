<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorksTable extends Migration 
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection(env('DB_CONNECTION_EMPLOYMENT', 'mysql_hr_employments'))->create('works', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('chart_id')->unsigned()->index();
			$table->integer('person_id')->unsigned()->index();
			$table->string('nik', 255);
			$table->string('status', 255);
			$table->date('start');
			$table->date('end');
			$table->text('reason_end_job');
			$table->timestamps();
			$table->softDeletes();

			$table->index(['deleted_at', 'nik']);
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
