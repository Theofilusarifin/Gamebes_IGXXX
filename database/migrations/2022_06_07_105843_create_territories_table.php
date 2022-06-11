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

            $table->tinyInteger('open_tr');
            $table->tinyInteger('close_tr');

            $table->tinyInteger('is_wall');
            $table->tinyInteger('is_water');
            $table->tinyInteger('is_harbour');
            $table->tinyInteger('is_company');

            $table->string('transport_store_id')->nullable()->references('id')->on('transport_stores')->onUpdate('cascade')->onDelete('cascade');
            // $table->foreign('transport_store_id')->references('id')->on('transport_stores')->onUpdate('cascade')->onDelete('cascade');

            $table->string('ingridient_store_id')->nullable()->references('id')->on('ingridient_stores')->onUpdate('cascade')->onDelete('cascade');
            // $table->foreign('ingridient_store_id')->references('id')->on('ingridient_stores')->onUpdate('cascade')->onDelete('cascade');

            $table->string('machine_store_id')->nullable()->references('id')->on('machine_stores')->onUpdate('cascade')->onDelete('cascade');
            // $table->foreign('machine_store_id')->references('id')->on('machine_stores')->onUpdate('cascade')->onDelete('cascade');

            $table->string('service_id')->nullable()->references('id')->on('services')->onUpdate('cascade')->onDelete('cascade');
            // $table->foreign('service_id')->references('id')->on('services')->onUpdate('cascade')->onDelete('cascade');

            $table->text('url_company')->nullable();
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
