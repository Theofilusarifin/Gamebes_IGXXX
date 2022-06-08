<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineMachineCombinationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_machine_combination', function (Blueprint $table) {
            $table->foreignId('machine_id');
            $table->foreign('machine_id')->references('id')->on('machines')->onUpdate('cascade')->onDelete('cascade');

            $table->foreignId('machine_combination_id');
            $table->foreign('machine_combination_id')->references('id')->on('machine_combinations')->onUpdate('cascade')->onDelete('cascade');

            $table->integer('order');
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
        Schema::dropIfExists('machine_machine_combination');
    }
}
