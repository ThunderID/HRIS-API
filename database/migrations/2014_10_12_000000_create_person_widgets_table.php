<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonWidgetsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('person_widgets', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('organisation_id')->unsigned()->index();
			$table->integer('person_id')->unsigned()->index();
			$table->enum('type', ['list', 'table', 'stat']);
			$table->string('widget', 255);
			$table->text('query');
			$table->string('dashboard', 255);
			$table->tinyinteger('row');
			$table->tinyinteger('col');
			$table->boolean('is_active');
			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('person_widgets');
	}

}
