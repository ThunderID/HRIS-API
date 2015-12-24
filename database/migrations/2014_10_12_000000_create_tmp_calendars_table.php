<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmpCalendarsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tmp_calendars', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('organisation_id')->unsigned()->index();
			$table->integer('import_from_id')->unsigned()->index();
			$table->string('name', 255);
			$table->text('workdays');
			$table->time('start');
			$table->time('end');
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
		Schema::drop('tmp_calendars');
	}

}
