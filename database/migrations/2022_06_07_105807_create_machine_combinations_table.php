<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineCombinationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_combinations', function (Blueprint $table) {
            $table->id();
            $table->integer('higenity');
            $table->integer('effectivity');
            $table->tinyInteger('produce_head');
            $table->tinyInteger('produce_skin');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('machine_combinations');
    }
}
