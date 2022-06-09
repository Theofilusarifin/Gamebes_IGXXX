<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIngridientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ingridients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->double('price');
            $table->foreignId('ingridient_store_id');
            $table->foreign('ingridient_store_id')->references('id')->on('ingridient_stores')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ingridients');
    }
}
