<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLimmiterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('limmiter', function (Blueprint $table) {
            $table->id();
            $table->decimal('minamount', 15, 2)->nullable();
            $table->decimal('maxamount', 15, 2)->nullable();
            $table->decimal('maxtrans', 15, 2)->nullable();            
            $table->smallInteger('status')->nullable();
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
        Schema::dropIfExists('limmiter');
    }
}
