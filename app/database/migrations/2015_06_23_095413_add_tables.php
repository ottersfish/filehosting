<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create('users', function($table){
			$table->increments('id');
			$table->string('email');
			$table->string('username');
			$table->string('password');
			$table->string('name');
			$table->boolean('is_admin');
			$table->timestamps();
		});

		Schema::create('files', function($table){
			$table->increments('id');
			$table->string('path');
			$table->string('key');
			$table->integer('id_user')->references('id')->on('users');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
		Schema::drop('files');
		Schema::drop('users');
	}

}
