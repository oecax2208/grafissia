<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaspayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faspay', function (Blueprint $table) {
            $table->id();
            $table->string('merchantname')->nullable();
            $table->string('merchantid')->nullable();
            $table->string('userid')->nullable();
            $table->string('password')->nullable();
            $table->string('redirecturl')->nullable();
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
        Schema::dropIfExists('faspay');
    }
}
