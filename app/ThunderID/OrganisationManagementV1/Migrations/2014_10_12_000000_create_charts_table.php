<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChartsTable extends Migration 
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::connection(env('DB_CONNECTION_ORGANISATION', 'mysql_hr_organisations'))->create('charts', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('branch_id')->unsigned()->index();
			$table->integer('chart_id')->unsigned()->index();
			$table->string('name', 255);
			$table->string('path', 255);
			$table->string('department', 255);
			$table->timestamps();
			$table->softDeletes();

			$table->index(['deleted_at', 'branch_id' ,'path']);
			$table->index(['deleted_at', 'branch_id' ,'department']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('charts');
	}

}
