<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('billing_name');
            $table->string('billing_lastname');
            $table->string('phone');
            $table->string('email');
            $table->longText('billing_address');
            $table->string('billing_address_city');
            $table->string('billing_address_region');
            $table->string('billing_address_state');
            $table->string('billing_address_poscode');
            $table->string('billing_address_country_code');            
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
        Schema::dropIfExists('bills');
    }
}
