<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->integer('id')->autoIncrement()->unique();
            $table->string('name',30);
            $table->string('contact_number',10)->unique();
            $table->string('email',30)->unique();
            $table->string('gender',12);
            $table->string('image',128)->nullable();
            $table->string('approved_by',30);          
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password',64);
            $table->rememberToken();
            $table->longText('registered_courses')->nullable();
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
        Schema::dropIfExists('users');
    }
}
