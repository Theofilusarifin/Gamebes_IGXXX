<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamMachineCombinationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team_machine_combination', function (Blueprint $table) {
            $table->foreignId('machine_combination_id');
            $table->foreign('machine_combination_id')->references('id')->on('machine_combinations')->onUpdate('cascade')->onDelete('cascade');

            $table->foreignId('team_id');
            $table->foreign('team_id')->references('id')->on('teams')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('team_machine_combination');
    }
}
