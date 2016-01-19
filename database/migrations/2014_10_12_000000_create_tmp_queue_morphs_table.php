<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmpQueueMorphsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('queue_morphs', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('queue_id')->unsigned()->index();
			$table->integer('queue_morph_id')->unsigned()->index();
			$table->string('queue_morph_type', 255);
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
		Schema::drop('queue_morphs');
	}

}
