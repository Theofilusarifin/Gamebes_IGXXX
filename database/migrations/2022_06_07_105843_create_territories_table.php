<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTerritoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('territories', function (Blueprint $table) {
            $table->id();
            $table->integer('rowspan');
            $table->integer('colspan');

            $table->tinyInteger('is_wall');
            $table->tinyInteger('is_water');
            $table->tinyInteger('is_harbour');
            $table->tinyInteger('is_company');

            $table->foreignId('transport_store_id');
            $table->foreign('transport_store_id')->references('id')->on('transport_stores')->onUpdate('cascade')->onDelete('cascade')->nullable();

            $table->foreignId('ingridient_store_id');
            $table->foreign('ingridient_store_id')->references('id')->on('ingridient_stores')->onUpdate('cascade')->onDelete('cascade')->nullable();

            $table->foreignId('machine_store_id');
            $table->foreign('machine_store_id')->references('id')->on('machine_stores')->onUpdate('cascade')->onDelete('cascade')->nullable();

            $table->foreignId('service_id');
            $table->foreign('service_id')->references('id')->on('services')->onUpdate('cascade')->onDelete('cascade')->nullable();

            $table->text('url_company')->nullable();

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
        Schema::dropIfExists('territories');
    }
}
