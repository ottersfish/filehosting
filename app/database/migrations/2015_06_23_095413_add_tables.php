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

        Schema::create('keys', function($table){
            $table->increments('id');
            $table->string('path');
            $table->string('key');
            $table->string('folder_key')->references('key')->on('folders');
            $table->integer('id_user')->references('id')->on('users');
            $table->timestamps();
        });

        Schema::create('files', function($table){
            $table->increments('id');
            $table->string('key')->references('key')->on('keys');
            $table->string('origFilename');
            $table->string('filename');
            $table->string('extension');
            $table->boolean('is_active');
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
        Schema::drop('users');
        Schema::drop('keys');
        Schema::drop('files');
    }

}
