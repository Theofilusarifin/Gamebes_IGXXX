<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransportTransportStoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transport_transport_store', function (Blueprint $table) {
            $table->foreignId('transport_id');
            $table->foreign('transport_id')->references('id')->on('transports')->onUpdate('cascade')->onDelete('cascade');

            $table->string('transport_store_id')->references('id')->on('transport_stores')->onUpdate('cascade')->onDelete('cascade');

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
        Schema::dropIfExists('transport_transport_store');
    }
}
