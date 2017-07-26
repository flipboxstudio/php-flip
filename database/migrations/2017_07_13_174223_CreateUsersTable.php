<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 128);
            $table->string('email', 64)->unique();
            $table->string('phone', 16)->unique()->nullable();
            $table->string('address', 512)->nullable();
            $table->enum('sex', ['M', 'F'])->nullable();
            $table->enum('role', [
                'ADM', // ADMIN
                'USR', // USER
            ], 'USR');
            $table->string('password', 512);
            $table->string('remember_token', 64)->nullable();
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
        Schema::drop('users');
    }
}
