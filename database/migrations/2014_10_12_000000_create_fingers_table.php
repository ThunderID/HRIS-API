<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFingersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('fingers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('person_id')->unsigned()->index();
			$table->text('left_thumb');
			$table->text('left_index_finger');
			$table->text('left_middle_finger');
			$table->text('left_ring_finger');
			$table->text('left_little_finger');
			$table->text('right_thumb');
			$table->text('right_index_finger');
			$table->text('right_middle_finger');
			$table->text('right_ring_finger');
			$table->text('right_little_finger');
			
			$table->timestamps();
			$table->softDeletes();
			
			$table->index(['deleted_at', 'person_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('fingers');
	}

}
