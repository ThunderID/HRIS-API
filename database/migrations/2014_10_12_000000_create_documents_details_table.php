<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentsDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('documents_details', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('person_document_id')->unsigned()->index();
			$table->integer('template_id')->unsigned()->index();
			$table->string('string', 255);
			$table->datetime('on');
			$table->double('numeric');
			$table->text('text');
			$table->timestamps();
			$table->softDeletes();

			$table->index(['deleted_at', 'on']);
			$table->index(['deleted_at', 'numeric']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('documents_details');
	}

}
