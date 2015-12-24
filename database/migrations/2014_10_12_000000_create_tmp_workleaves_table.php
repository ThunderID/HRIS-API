<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmpWorkleavesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tmp_workleaves', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('organisation_id')->unsigned()->index();
			$table->string('name', 255);
			$table->integer('quota');
			$table->enum('status', ['CB', 'CN', 'CI']);
			$table->boolean('is_active');
			$table->timestamps();
			$table->softDeletes();
			
			$table->index(['deleted_at', 'organisation_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tmp_workleaves');
	}

}
