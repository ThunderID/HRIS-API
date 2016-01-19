<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChartsWorkleavesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('charts_workleaves', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('chart_id')->unsigned()->index();
			$table->integer('workleave_id')->unsigned()->index();
			$table->text('rules');
			$table->timestamps();
			$table->softDeletes();
			
			$table->index(['deleted_at', 'chart_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('charts_workleaves');
	}

}
