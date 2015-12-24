<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFingerPrintsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('finger_prints', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('branch_id')->unsigned()->index();
			$table->boolean('left_thumb');
			$table->boolean('left_index_finger');
			$table->boolean('left_middle_finger');
			$table->boolean('left_ring_finger');
			$table->boolean('left_little_finger');
			$table->boolean('right_thumb');
			$table->boolean('right_index_finger');
			$table->boolean('right_middle_finger');
			$table->boolean('right_ring_finger');
			$table->boolean('right_little_finger');
			
			$table->timestamps();
			$table->softDeletes();
			
			$table->index(['deleted_at', 'branch_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('finger_prints');
	}

}
