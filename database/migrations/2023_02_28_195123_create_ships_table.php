<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ships', function (Blueprint $table) {
            $table->id();
            $table->string('shiping_name');
            $table->string('shiping_lastname');
            $table->string('phone');
            $table->string('email');
            $table->longText('shiping_address');
            $table->string('shiping_address_city');
            $table->string('shiping_address_region');
            $table->string('shiping_address_state');
            $table->string('shiping_address_poscode');
            $table->string('shiping_address_country_code'); 
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
        Schema::dropIfExists('ships');
    }
}
