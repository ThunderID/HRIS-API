<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonWorkleavesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('person_workleaves', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('person_id')->unsigned()->index();
			$table->integer('work_id')->unsigned()->index();
			$table->integer('workleave_id')->unsigned()->index();
			$table->integer('person_workleave_id')->unsigned()->index();
			$table->integer('created_by')->unsigned()->index();
			$table->string('name', 255);
			$table->date('start');
			$table->date('end');
			$table->integer('quota');
			$table->enum('status', ['OFFER', 'CB', 'CN', 'CI', 'CONFIRMED']);
			$table->text('notes');
			$table->timestamps();
			$table->softDeletes();
			
			$table->index(['deleted_at', 'person_id', 'start']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('person_workleaves');
	}

}
