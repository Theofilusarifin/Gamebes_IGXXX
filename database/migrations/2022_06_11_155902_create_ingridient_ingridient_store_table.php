<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIngridientIngridientStoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ingridient_ingridient_store', function (Blueprint $table) {
            $table->foreignId('ingridient_id');
            $table->foreign('ingridient_id')->references('id')->on('ingridients')->onUpdate('cascade')->onDelete('cascade');

            $table->string('ingridient_store_id')->references('id')->on('ingridient_stores')->onUpdate('cascade')->onDelete('cascade');

            $table->integer('stock');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ingridient_ingridient_store');
    }
}
