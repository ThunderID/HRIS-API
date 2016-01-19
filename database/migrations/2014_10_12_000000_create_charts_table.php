<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChartsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('charts', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('branch_id')->unsigned()->index();
			$table->integer('chart_id')->unsigned()->index();
			$table->string('name', 255);
			$table->string('path', 255);
			$table->string('tag', 255);
			$table->integer('min_employee');
			$table->integer('ideal_employee');
			$table->integer('max_employee');
			$table->integer('current_employee');
			$table->timestamps();
			$table->softDeletes();

			$table->index(['deleted_at', 'branch_id' ,'path']);
			$table->index(['deleted_at', 'branch_id' ,'tag']);
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
