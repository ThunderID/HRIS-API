<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGradeLogsTable extends Migration 
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection(env('DB_CONNECTION_EMPLOYMENT', 'mysql_hr_employments'))->create('grade_logs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('work_id')->unsigned()->index();
			$table->integer('grade');
			$table->timestamps();
			$table->softDeletes();

			$table->index(['deleted_at', 'grade', 'created_at']);
			$table->index(['deleted_at', 'work_id', 'created_at']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('grade_logs');
	}

}
