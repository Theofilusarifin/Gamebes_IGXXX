<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->integer('tc');
            $table->integer('level');

            $table->integer('s_moves')->nullable();
            $table->integer('d_moves')->nullable();
            $table->integer('total_income')->nullable();
            $table->integer('total_spend')->nullable();

            $table->integer('machine_assembly')->nullable();
            $table->integer('total_spawn')->nullable();
            $table->integer('total_crash')->nullable();
            $table->integer('total_maintenance')->nullable();
            $table->integer('waste')->nullable();

            $table->foreignId('territory_id')->nullable();
            $table->foreign('territory_id')->references('id')->on('territories')->onUpdate('cascade')->onDelete('cascade');

            $table->foreignId('service_id')->nullable();
            $table->foreign('service_id')->references('id')->on('services')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teams');
    }
}
