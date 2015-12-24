<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTmpGroupMenusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tmp_groups_menus', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('tmp_auth_group_id')->unsigned()->index();
			$table->integer('tmp_menu_id')->unsigned()->index();
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
		Schema::drop('tmp_groups_menus');
	}

}
