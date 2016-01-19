<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFollowWorkleavesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('follow_workleaves', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('workleave_id')->unsigned()->index();
			$table->integer('work_id')->unsigned()->index();
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
		Schema::drop('follow_workleaves');
	}

}
