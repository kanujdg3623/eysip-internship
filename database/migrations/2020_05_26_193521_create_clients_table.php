<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->integer('id')->autoIncrement()->unique();
            $table->uuid('client_id')->unique();
            $table->timestamps();
            $table->string('IP',30);
            $table->string('city',100);
            $table->string('state',100);
            $table->string('country',100);
            $table->float('longitude', 8, 2);
            $table->float('latitude', 8, 2);
            $table->text('os_browser');
            $table->longText('vis_session_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients');
    }
}
