<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateErrorLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('error_logs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('organisation_id')->unsigned()->index();
			$table->string('email', 255);
			$table->string('name', 255);
			$table->string('pc', 255);
			$table->datetime('on');
			$table->text('message');
			$table->string('ip', 255);
			$table->timestamps();
			$table->softDeletes();
			
			$table->index(['deleted_at', 'organisation_id', 'on']);
			
			$table->index(['deleted_at', 'on']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('error_logs');
	}

}
