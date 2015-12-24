<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmpSchedulesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tmp_schedules', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('calendar_id')->unsigned()->index();
			$table->integer('created_by')->unsigned()->index();
			$table->string('name', 255);
			$table->enum('status', ['DN', 'SS', 'SL', 'CN', 'CB', 'CI', 'UL', 'HB', 'L']);
			$table->date('on');
			$table->time('start');
			$table->time('end');
			$table->timestamps();
			$table->softDeletes();
			
			$table->index(['deleted_at', 'calendar_id', 'on', 'status']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tmp_schedules');
	}

}
