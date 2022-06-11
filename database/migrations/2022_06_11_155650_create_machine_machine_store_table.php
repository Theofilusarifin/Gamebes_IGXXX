<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineMachineStoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_machine_store', function (Blueprint $table) {
            $table->foreignId('machine_id');
            $table->foreign('machine_id')->references('id')->on('machines')->onUpdate('cascade')->onDelete('cascade');

            $table->string('machine_store_id')->references('id')->on('machine_stores')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('machine_machine_store');
    }
}
